<?php

namespace App\Models;

use App\Services\PrivateMediaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'month_year',
        'amount',
        'breakdown',
        'status',
        'payment_method',
        'paid_at',
        'payment_proof_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'breakdown' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return PrivateMediaService::signedUrl($this->payment_proof_path);
    }
}

