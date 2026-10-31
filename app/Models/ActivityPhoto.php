<?php

namespace App\Models;

use App\Services\PrivateMediaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'category',
        'photo_path',
        'caption',
        'taken_time',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return PrivateMediaService::signedUrl($this->photo_path);
    }
}

