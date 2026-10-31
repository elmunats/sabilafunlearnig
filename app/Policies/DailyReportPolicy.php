<?php

namespace App\Policies;

use App\Models\DailyReport;
use App\Models\User;

class DailyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DailyReport $report): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        return (int) $report->child->parent_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, DailyReport $report): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function delete(User $user, DailyReport $report): bool
    {
        return $user->isAdmin();
    }
}

