<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_title',
        'product_slug',
        'price_label',
        'price_numeric',
        'quantity',
        'total_numeric',
        'download_path',
        'download_name',
    ];

    protected $casts = [
        'price_numeric' => 'integer',
        'quantity' => 'integer',
        'total_numeric' => 'integer',
    ];

    protected $appends = [
        'formatted_total',
        'download_available',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_numeric, 0, ',', '.');
    }

    public function getDownloadAvailableAttribute(): bool
    {
        return filled($this->resolveDownloadPath());
    }

    public function resolveDownloadPath(): ?string
    {
        return $this->download_path ?: $this->product?->source_code_path;
    }

    public function resolveDownloadName(): ?string
    {
        return $this->download_name ?: $this->product?->source_code_original_name;
    }
}
