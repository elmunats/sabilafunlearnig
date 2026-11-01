<?php

namespace App\Models;

use App\Services\PrivateMediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'parent_id',
        'class_group_id',
        'name',
        'photo',
        'birth_date',
        'gender',
        'address',
        'group_name',
        'enrollment_status',
        'approved_at',
        'join_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Scope query strictly to children accessible by the given user (Row-Level Security).
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTeacher()) {
            $mappedClassIds = $user->teachingClasses()->pluck('class_groups.id')->toArray();
            if (! empty($mappedClassIds)) {
                return $query->whereIn('class_group_id', $mappedClassIds);
            }

            return $query;
        }

        return $query->where('parent_id', $user->id);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'child_id')->orderByDesc('report_date');
    }

    public function monthlyReports(): HasMany
    {
        return $this->hasMany(MonthlyReport::class, 'child_id')->orderByDesc('month_year');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'child_id')->orderByDesc('month_year');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return PrivateMediaService::signedUrl($this->photo);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->enrollment_status) {
            'pending' => 'Menunggu Persetujuan',
            'rejected' => 'Tidak Disetujui',
            default => 'Aktif Terdaftar',
        };
    }
}
