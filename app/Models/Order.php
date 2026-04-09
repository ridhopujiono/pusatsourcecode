<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'midtrans_order_id',
        'customer_name',
        'customer_email',
        'subtotal_amount',
        'total_amount',
        'payment_status',
        'midtrans_transaction_status',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_fraud_status',
        'snap_token',
        'snap_redirect_url',
        'snap_payload',
        'payment_payload',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'subtotal_amount' => 'integer',
        'total_amount' => 'integer',
        'snap_payload' => 'array',
        'payment_payload' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_total',
        'status_label',
        'status_badge_classes',
        'is_paid',
        'can_retry_payment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->orderBy('id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'border-green-200 bg-green-50 text-green-800',
            'failed', 'expired', 'cancelled' => 'border-red-200 bg-red-50 text-red-700',
            default => 'border-amber-200 bg-amber-50 text-amber-800',
        };
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getCanRetryPaymentAttribute(): bool
    {
        return in_array($this->payment_status, ['pending', 'failed', 'expired', 'cancelled'], true);
    }
}
