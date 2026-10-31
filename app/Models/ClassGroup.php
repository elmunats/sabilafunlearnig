<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quota',
        'schedule',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
        ];
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_group_teacher', 'class_group_id', 'teacher_id')
            ->withTimestamps();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'class_group_id');
    }

    public function approvedChildren(): HasMany
    {
        return $this->hasMany(Child::class, 'class_group_id')
            ->where('enrollment_status', 'approved');
    }

    public function pendingChildren(): HasMany
    {
        return $this->hasMany(Child::class, 'class_group_id')
            ->where('enrollment_status', 'pending');
    }

    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTeacher()) {
            $hasMappedClasses = $user->teachingClasses()->exists();
            if ($hasMappedClasses) {
                return $query->whereHas('teachers', fn ($q) => $q->where('users.id', $user->id));
            }
        }

        return $query;
    }
}

