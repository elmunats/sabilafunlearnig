<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ClassGroup;
use App\Models\User;
use App\Services\PrivateMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();
        $children = Child::accessibleBy($user)->with(['parent', 'classGroup'])->orderBy('id')->get();
        $activeChildId = (int) $request->query('child_id', $request->session()->get('active_child_id'));
        $activeChild = $children->firstWhere('id', $activeChildId) ?? $children->first();

        if ($activeChild) {
            Gate::authorize('view', $activeChild);
            $request->session()->put('active_child_id', $activeChild->id);
        }

        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $childProfile = null;
        if ($activeChild) {
            $bd = $activeChild->birth_date;
            $jd = $activeChild->join_date;
            $groupName = $activeChild->classGroup?->name ?? $activeChild->group_name;
            $shortGroup = preg_replace('/^Kelompok\s+/i', '', $groupName);

            $childProfile = [
                'id' => $activeChild->id,
                'name' => $activeChild->name,
                'class_group_id' => $activeChild->class_group_id,
                'group_name' => $groupName,
                'short_group' => $shortGroup,
                'enrollment_status' => $activeChild->enrollment_status ?? 'approved',
                'status_label' => $activeChild->status_label,
                'birth_date' => $bd->format('Y-m-d'),
                'birth_date_formatted' => $bd->format('d') . ' ' . ($monthsFull[(int) $bd->format('n')] ?? '') . ' ' . $bd->format('Y'),
                'join_date' => $jd->format('Y-m-d'),
                'join_date_formatted' => ($monthsFull[(int) $jd->format('n')] ?? '') . ' ' . $jd->format('Y'),
                'photo_url' => $activeChild->photo_url,
                'parent' => [
                    'id' => $activeChild->parent?->id,
                    'name' => $activeChild->parent?->name ?? $user->name,
                    'phone' => $activeChild->parent?->phone ?? $user->phone,
                    'email' => $activeChild->parent?->email ?? $user->email,
                    'address' => $activeChild->parent?->address ?? $user->address,
                ],
            ];
        }

        $parentOptions = $user->isAdmin()
            ? User::where('role', 'parent')->select('id', 'name', 'email')->orderBy('name')->get()
            : [];

        return Inertia::render('Profile/Show', [
            'childProfile' => $childProfile,
            'parentOptions' => $parentOptions,
        ]);
    }

    public function update(Request $request, Child $child): RedirectResponse
    {
        Gate::authorize('update', $child);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'group_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'join_date' => ['required', 'date'],
            'parent_name' => ['required', 'string', 'max:120'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'parent_email' => ['required', 'email', 'max:190'],
            'parent_address' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $child->photo = PrivateMediaService::store($request->file('photo'), 'avatars');
        }

        $classGroup = ClassGroup::where('name', $validated['group_name'])->first();
        if ($classGroup) {
            $child->class_group_id = $classGroup->id;
        }

        $child->name = $validated['name'];
        $child->group_name = $validated['group_name'];
        $child->birth_date = $validated['birth_date'];
        $child->join_date = $validated['join_date'];
        $child->save();

        if ($child->parent) {
            $child->parent->update([
                'name' => $validated['parent_name'],
                'phone' => $validated['parent_phone'],
                'email' => $validated['parent_email'],
                'address' => $validated['parent_address'],
            ]);
        }

        return back()->with('success', 'Profil anak dan data orang tua berhasil diperbarui.');
    }

    public function storeChild(Request $request): RedirectResponse
    {
        Gate::authorize('create', Child::class);

        $validated = $request->validate([
            'parent_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:120'],
            'group_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'join_date' => ['required', 'date'],
        ]);

        $classGroup = ClassGroup::where('name', $validated['group_name'])->first();

        $child = Child::create([
            ...$validated,
            'class_group_id' => $classGroup?->id,
            'enrollment_status' => $request->user()->isAdmin() ? 'approved' : 'pending',
            'approved_at' => $request->user()->isAdmin() ? now() : null,
            'photo' => 'avatars/aisyah.svg',
        ]);

        $request->session()->put('active_child_id', $child->id);

        return back()->with('success', 'Anak baru (' . $child->name . ') berhasil didaftarkan.');
    }
}
