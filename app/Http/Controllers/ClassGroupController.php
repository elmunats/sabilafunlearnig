<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Child;
use App\Models\ClassGroup;
use App\Models\User;
use App\Services\PrivateMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class ClassGroupController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isTeacher()) {
            abort(403, 'Halaman manajemen kelas hanya untuk Guru dan Admin.');
        }

        // Classes accessible to this user (Admin sees all; Teacher sees their mapped classes)
        $myClassesQuery = ClassGroup::accessibleBy($user)
            ->with([
                'teachers:id,name,email,phone',
                'approvedChildren.parent:id,name,phone,email',
                'pendingChildren.parent:id,name,phone,email',
            ])
            ->withCount([
                'approvedChildren as approved_count',
                'pendingChildren as pending_count',
            ])
            ->orderBy('id');

        $myClasses = $myClassesQuery->get();

        // All classes in the system (so a Teacher who teaches more than one class can also add/join another class, and Admin can manage all)
        $allClasses = ClassGroup::with('teachers:id,name,email')
            ->withCount([
                'approvedChildren as approved_count',
                'pendingChildren as pending_count',
            ])
            ->orderBy('id')
            ->get();

        $selectedClassId = (int) $request->query('class_id', $myClasses->first()?->id ?? $allClasses->first()?->id);
        $activeClass = $myClasses->firstWhere('id', $selectedClassId)
            ?? ($user->isAdmin() ? $allClasses->firstWhere('id', $selectedClassId) : null)
            ?? $myClasses->first();

        $pendingChildren = Child::with(['parent:id,name,phone,email', 'classGroup:id,name,quota'])
            ->where('enrollment_status', 'pending')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Child $c) => $this->transformChild($c))
            ->values()
            ->all();

        $allTeachers = User::where('role', 'teacher')
            ->with('teachingClasses:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (User $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'email' => $t->email,
                'phone' => $t->phone,
                'class_ids' => $t->teachingClasses->pluck('id')->values()->all(),
                'class_names' => $t->teachingClasses->pluck('name')->values()->all(),
            ])
            ->values()
            ->all();

        $parentOptions = User::where('role', 'parent')
            ->select('id', 'name', 'email', 'phone')
            ->orderBy('name')
            ->get();

        return Inertia::render('Classes/Index', [
            'myClasses' => $myClasses->map(fn (ClassGroup $cg) => $this->transformClassGroup($cg))->values()->all(),
            'allClasses' => $allClasses->map(fn (ClassGroup $cg) => [
                'id' => $cg->id,
                'name' => $cg->name,
                'category' => $cg->category,
                'quota' => $cg->quota,
                'schedule' => $cg->schedule,
                'approved_count' => $cg->approved_count,
                'pending_count' => $cg->pending_count,
                'remaining_quota' => max(0, $cg->quota - $cg->approved_count),
                'teacher_ids' => $cg->teachers->pluck('id')->values()->all(),
                'teachers' => $cg->teachers->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values()->all(),
            ])->values()->all(),
            'activeClass' => $activeClass ? $this->transformClassGroup($activeClass) : null,
            'pendingChildren' => $pendingChildren,
            'allTeachers' => $allTeachers,
            'parentOptions' => $parentOptions,
        ]);
    }

    /**
     * Parent or Admin registers a child for a class.
     * Parent registrations enter 'pending' status waiting for Admin approval.
     */
    public function enrollChild(Request $request): RedirectResponse
    {
        $user = $request->user();
        Gate::authorize('create', Child::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'birth_date' => ['required', 'date'],
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'parent_id' => ['nullable', 'exists:users,id'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);

        $classGroup = ClassGroup::findOrFail($validated['class_group_id']);
        $targetParentId = ($user->isAdmin() && ! empty($validated['parent_id']))
            ? (int) $validated['parent_id']
            : $user->id;

        // If Admin creates directly, check quota immediately and approve if space available
        $isDirectAdminApprove = $user->isAdmin();
        if ($isDirectAdminApprove) {
            $approvedCount = $classGroup->approvedChildren()->count();
            if ($approvedCount >= $classGroup->quota) {
                throw ValidationException::withMessages([
                    'class_group_id' => "Kuota {$classGroup->name} sudah penuh ({$approvedCount}/{$classGroup->quota}). Silakan tambah kuota terlebih dahulu.",
                ]);
            }
        }

        $photoPath = 'avatars/syifa.svg';
        if ($request->hasFile('photo')) {
            $photoPath = PrivateMediaService::store($request->file('photo'), 'avatars');
        }

        $status = $isDirectAdminApprove ? 'approved' : 'pending';

        $child = Child::create([
            'parent_id' => $targetParentId,
            'class_group_id' => $classGroup->id,
            'name' => $validated['name'],
            'photo' => $photoPath,
            'birth_date' => $validated['birth_date'],
            'group_name' => $classGroup->name,
            'enrollment_status' => $status,
            'approved_at' => $status === 'approved' ? now() : null,
            'join_date' => now()->toDateString(),
        ]);

        $request->session()->put('active_child_id', $child->id);

        if ($status === 'pending') {
            // Notify Parent
            AppNotification::create([
                'user_id' => $targetParentId,
                'child_id' => $child->id,
                'type' => 'info',
                'title' => 'Pendaftaran ' . $child->name . ' menunggu persetujuan Admin',
                'message' => 'Pengajuan masuk ' . $classGroup->name . ' untuk ' . $child->name . ' telah diterima dan sedang diverifikasi kuota oleh Admin.',
                'action_url' => route('profile.show', [], false),
                'is_read' => false,
            ]);

            // Notify Admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                AppNotification::create([
                    'user_id' => $admin->id,
                    'child_id' => $child->id,
                    'type' => 'info',
                    'title' => 'Pendaftaran siswa baru: ' . $child->name,
                    'message' => $user->name . ' mendaftarkan ' . $child->name . ' ke ' . $classGroup->name . ' (Menunggu Persetujuan).',
                    'action_url' => route('classes.index', ['class_id' => $classGroup->id], false),
                    'is_read' => false,
                ]);
            }

            return back()->with(
                'success',
                'Pendaftaran ' . $child->name . ' ke ' . $classGroup->name . ' berhasil dikirim dan sedang menunggu persetujuan Admin.'
            );
        }

        return back()->with('success', 'Siswa ' . $child->name . ' berhasil didaftarkan ke ' . $classGroup->name . '.');
    }

    /**
     * Admin approves or rejects a child's class enrollment with strict quota validation.
     */
    public function approveEnrollment(Request $request, Child $child): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menyetujui pendaftaran kelas.');
        }

        $validated = $request->validate([
            'enrollment_status' => ['required', 'in:approved,rejected,pending'],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
        ]);

        $targetClassId = $validated['class_group_id'] ?? $child->class_group_id;
        $classGroup = ClassGroup::findOrFail($targetClassId);

        if ($validated['enrollment_status'] === 'approved') {
            $currentApproved = $classGroup->approvedChildren()
                ->where('children.id', '!=', $child->id)
                ->count();

            if ($currentApproved >= $classGroup->quota) {
                return back()->with(
                    'error',
                    "Gagal menyetujui: Kuota {$classGroup->name} sudah penuh ({$currentApproved}/{$classGroup->quota}). Silakan naikkan kuota kelas atau pindahkan ke kelas lain."
                );
            }
        }

        $child->update([
            'class_group_id' => $classGroup->id,
            'group_name' => $classGroup->name,
            'enrollment_status' => $validated['enrollment_status'],
            'approved_at' => $validated['enrollment_status'] === 'approved' ? now() : null,
        ]);

        if ($validated['enrollment_status'] === 'approved') {
            AppNotification::create([
                'user_id' => $child->parent_id,
                'child_id' => $child->id,
                'type' => 'info',
                'title' => 'Pendaftaran ' . $child->name . ' telah disetujui!',
                'message' => 'Selamat Bunda, ' . $child->name . ' telah resmi terdaftar di ' . $classGroup->name . '.',
                'action_url' => route('dashboard', [], false),
                'is_read' => false,
            ]);

            return back()->with('success', 'Pendaftaran ' . $child->name . ' di ' . $classGroup->name . ' telah disetujui.');
        }

        if ($validated['enrollment_status'] === 'rejected') {
            AppNotification::create([
                'user_id' => $child->parent_id,
                'child_id' => $child->id,
                'type' => 'info',
                'title' => 'Status pendaftaran ' . $child->name,
                'message' => 'Mohon maaf Bunda, kuota ' . $classGroup->name . ' saat ini belum tersedia. Silakan hubungi Admin untuk jadwal kelas alternatif.',
                'action_url' => route('profile.show', [], false),
                'is_read' => false,
            ]);

            return back()->with('success', 'Pendaftaran ' . $child->name . ' ditandai belum disetujui.');
        }

        return back()->with('success', 'Status pendaftaran ' . $child->name . ' diperbarui.');
    }

    /**
     * Only Admin can create a new ClassGroup.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menambahkan kelas baru.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:class_groups,name'],
            'category' => ['required', 'in:Calistung,Pracalis'],
            'quota' => ['required', 'integer', 'min:1', 'max:50'],
            'schedule' => ['required', 'string', 'max:100'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:users,id'],
        ]);

        $classGroup = ClassGroup::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'quota' => $validated['quota'],
            'schedule' => $validated['schedule'],
        ]);

        if (! empty($validated['teacher_ids'])) {
            $classGroup->teachers()->sync($validated['teacher_ids']);
        }

        return redirect()
            ->route('classes.index', ['class_id' => $classGroup->id])
            ->with('success', 'Kelas baru (' . $classGroup->name . ') dengan kuota ' . $classGroup->quota . ' siswa berhasil ditambahkan.');
    }

    /**
     * Admin updates class quota, name, category, and schedule.
     */
    public function updateQuota(Request $request, ClassGroup $classGroup): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat mengubah kuota kelas.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:Calistung,Pracalis'],
            'quota' => ['required', 'integer', 'min:1', 'max:100'],
            'schedule' => ['required', 'string', 'max:100'],
        ]);

        $classGroup->update($validated);

        // Keep children.group_name synchronized if class name changed
        Child::where('class_group_id', $classGroup->id)->update(['group_name' => $classGroup->name]);

        return back()->with('success', 'Kuota & informasi ' . $classGroup->name . ' berhasil diperbarui (Kuota: ' . $classGroup->quota . ' siswa).');
    }

    /**
     * Only Admin maps Teachers to a ClassGroup.
     */
    public function syncTeachers(Request $request, ClassGroup $classGroup): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat melakukan mapping guru ke kelas.');
        }

        $validated = $request->validate([
            'teacher_ids' => ['present', 'array'],
            'teacher_ids.*' => ['exists:users,id'],
        ]);

        $classGroup->teachers()->sync($validated['teacher_ids']);

        return back()->with('success', 'Mapping guru untuk ' . $classGroup->name . ' berhasil disimpan.');
    }

    /**
     * Only Admin assigns a teacher to an additional class.
     */
    public function attachTeacherClass(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menambahkan kelas mengajar untuk guru.');
        }

        $validated = $request->validate([
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'teacher_id' => ['required', 'exists:users,id'],
        ]);

        $targetTeacherId = (int) $validated['teacher_id'];

        $classGroup = ClassGroup::findOrFail($validated['class_group_id']);
        $classGroup->teachers()->syncWithoutDetaching([$targetTeacherId]);

        return redirect()
            ->route('classes.index', ['class_id' => $classGroup->id])
            ->with('success', 'Kelas ' . $classGroup->name . ' berhasil ditambahkan ke daftar kelas mengajar guru.');
    }

    /**
     * Admin creates a new Teacher and maps them to one or more classes.
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['exists:class_groups,id'],
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'teacher',
            'address' => 'Sabila Fun Learning',
            'password' => 'password123',
        ]);

        if (class_exists(Role::class)) {
            $role = Role::firstOrCreate(['name' => 'teacher']);
            $teacher->assignRole($role);
        }

        if (! empty($validated['class_ids'])) {
            $teacher->teachingClasses()->sync($validated['class_ids']);
        }

        return back()->with('success', 'Guru baru (' . $teacher->name . ') berhasil ditambahkan dan di-mapping ke kelas.');
    }

    private function transformClassGroup(ClassGroup $cg): array
    {
        $approvedCount = $cg->approved_count ?? $cg->approvedChildren->count();
        $pendingCount = $cg->pending_count ?? $cg->pendingChildren->count();

        return [
            'id' => $cg->id,
            'name' => $cg->name,
            'category' => $cg->category,
            'quota' => $cg->quota,
            'schedule' => $cg->schedule,
            'approved_count' => $approvedCount,
            'pending_count' => $pendingCount,
            'remaining_quota' => max(0, $cg->quota - $approvedCount),
            'is_full' => $approvedCount >= $cg->quota,
            'teacher_ids' => $cg->teachers->pluck('id')->values()->all(),
            'teachers' => $cg->teachers->map(fn (User $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'email' => $t->email,
                'phone' => $t->phone,
            ])->values()->all(),
            'approved_children' => $cg->approvedChildren->map(fn (Child $c) => $this->transformChild($c))->values()->all(),
            'pending_children' => $cg->pendingChildren->map(fn (Child $c) => $this->transformChild($c))->values()->all(),
        ];
    }

    private function transformChild(Child $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'class_group_id' => $c->class_group_id,
            'group_name' => $c->classGroup?->name ?? $c->group_name,
            'enrollment_status' => $c->enrollment_status,
            'status_label' => $c->status_label,
            'birth_date' => $c->birth_date?->format('d M Y'),
            'join_date' => $c->join_date?->format('d M Y'),
            'photo_url' => $c->photo_url,
            'parent_name' => $c->parent?->name ?? '-',
            'parent_phone' => $c->parent?->phone ?? '-',
        ];
    }
}

