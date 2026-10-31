<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;

class ChildPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Child $child): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        return (int) $child->parent_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isParent();
    }

    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Child $child): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $child->parent_id === (int) $user->id;
    }

    public function delete(User $user, Child $child): bool
    {
        return $user->isAdmin();
    }
}
