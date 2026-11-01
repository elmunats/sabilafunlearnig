<?php

namespace App\Http\Middleware;

use App\Models\Child;
use App\Models\ClassGroup;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $children = collect();
        $activeChild = null;
        $unreadNotificationsCount = 0;
        $classGroups = collect();
        $pendingEnrollmentsCount = 0;

        if ($user) {
            $childrenModels = Child::accessibleBy($user)
                ->with(['parent:id,name,phone,email,address', 'classGroup:id,name,category,quota,schedule'])
                ->orderBy('id')
                ->get();

            $activeChildId = $request->session()->get('active_child_id');
            $activeModel = $childrenModels->firstWhere('id', (int) $activeChildId) ?? $childrenModels->first();

            if ($activeModel && (int) $activeChildId !== (int) $activeModel->id) {
                $request->session()->put('active_child_id', $activeModel->id);
            }

            $children = $childrenModels->map(fn (Child $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'class_group_id' => $c->class_group_id,
                'group_name' => $c->classGroup?->name ?? $c->group_name,
                'enrollment_status' => $c->enrollment_status ?? 'approved',
                'status_label' => $c->status_label,
                'birth_date' => $c->birth_date?->format('Y-m-d'),
                'gender' => $c->gender,
                'address' => $c->address ?? $c->parent?->address,
                'join_date' => $c->join_date?->format('Y-m-d'),
                'photo_url' => $c->photo_url,
                'parent' => $c->parent ? [
                    'id' => $c->parent->id,
                    'name' => $c->parent->name,
                    'phone' => $c->parent->phone,
                    'email' => $c->parent->email,
                    'address' => $c->parent->address,
                ] : null,
            ])->values();

            $activeChild = $activeModel ? $children->firstWhere('id', $activeModel->id) : null;

            $unreadNotificationsCount = $user->appNotifications()
                ->where('is_read', false)
                ->count();

            $classGroups = ClassGroup::withCount([
                'approvedChildren as approved_count',
                'pendingChildren as pending_count',
            ])
                ->with('teachers:id,name,email')
                ->orderBy('id')
                ->get()
                ->map(fn (ClassGroup $cg) => [
                    'id' => $cg->id,
                    'name' => $cg->name,
                    'category' => $cg->category,
                    'quota' => $cg->quota,
                    'schedule' => $cg->schedule,
                    'approved_count' => $cg->approved_count,
                    'pending_count' => $cg->pending_count,
                    'remaining_quota' => max(0, $cg->quota - $cg->approved_count),
                    'is_full' => $cg->approved_count >= $cg->quota,
                    'teachers' => $cg->teachers->map(fn ($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                    ])->values()->all(),
                ])->values();

            if ($user->isAdmin() || $user->isTeacher()) {
                $pendingEnrollmentsCount = Child::where('enrollment_status', 'pending')->count();
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'address' => $user->address,
                    'can_manage_reports' => $user->isAdmin() || $user->isTeacher(),
                    'is_admin' => $user->isAdmin(),
                    'is_teacher' => $user->isTeacher(),
                    'is_parent' => $user->isParent(),
                ] : null,
                'children' => $children,
                'activeChild' => $activeChild,
                'classGroups' => $classGroups,
                'pendingEnrollmentsCount' => $pendingEnrollmentsCount,
                'unreadNotificationsCount' => $unreadNotificationsCount,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
