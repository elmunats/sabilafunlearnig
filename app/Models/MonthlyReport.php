<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'month_year',
        'reading_score',
        'writing_score',
        'counting_score',
        'attitude_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reading_score' => 'integer',
            'writing_score' => 'integer',
            'counting_score' => 'integer',
            'attitude_score' => 'integer',
            'notes' => 'array',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}

