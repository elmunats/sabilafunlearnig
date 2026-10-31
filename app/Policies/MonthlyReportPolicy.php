<?php

namespace App\Policies;

use App\Models\MonthlyReport;
use App\Models\User;

class MonthlyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MonthlyReport $report): bool
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

    public function update(User $user, MonthlyReport $report): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }
}

