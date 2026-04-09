<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'price_numeric',
        'category',
        'tech_stack',
        'features',
        'delivery',
        'image_path',
        'list_thumbnail_path',
        'detail_thumbnail_path',
        'source_code_path',
        'source_code_original_name',
        'updated_label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'price_numeric' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'whatsapp_url',
        'image_url',
        'list_thumbnail_url',
        'detail_thumbnail_url',
        'has_source_code_file',
    ];

    public function getWhatsappUrlAttribute(): string
    {
        $message = "Halo, saya tertarik untuk membeli source code {$this->title} yang ada di pusatsourcecode.site";

        return 'https://wa.me/6282257802227?text='.urlencode($message);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->image_path);
    }

    public function getListThumbnailUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->list_thumbnail_path);
    }

    public function getDetailThumbnailUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->detail_thumbnail_path);
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(ProductScreenshot::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)
            ->latest('created_at')
            ->latest('id');
    }

    public function getHasSourceCodeFileAttribute(): bool
    {
        return filled($this->source_code_path);
    }

    private function resolvePublicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
