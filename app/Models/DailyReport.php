<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'teacher_id',
        'report_date',
        'attendance_status',
        'absence_reason',
        'time_range',
        'materials',
        'activities',
        'behavior',
        'teacher_notes',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date:Y-m-d',
            'materials' => 'array',
            'activities' => 'array',
            'behavior' => 'array',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function activityPhotos(): HasMany
    {
        return $this->hasMany(ActivityPhoto::class, 'daily_report_id');
    }
}

