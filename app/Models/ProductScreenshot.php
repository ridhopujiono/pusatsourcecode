<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductScreenshot extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'thumbnail_path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'thumbnail_url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->image_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->thumbnail_path);
    }

    private function resolvePublicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
