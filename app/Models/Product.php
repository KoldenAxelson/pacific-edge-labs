<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'sku', 'name', 'slug', 'short_description',
        'description', 'form', 'concentration', 'storage_conditions',
        'price', 'compare_price', 'meta_title', 'meta_description',
        'featured', 'active',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'featured'      => 'boolean',
        'active'        => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function researchLinks(): HasMany
    {
        return $this->hasMany(ProductResearchLink::class)->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeInCategory(Builder $query, string|int $category): Builder
    {
        if (is_string($category)) {
            return $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }
        return $query->where('category_id', $category);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $safe = '%' . trim($term) . '%';
        // ILIKE for PostgreSQL (case-insensitive), LIKE for SQLite (case-insensitive by default for ASCII)
        $like = $query->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        return $query->where(function ($q) use ($safe, $like) {
            $q->whereRaw("name {$like} ?", [$safe])
              ->orWhereRaw("description {$like} ?", [$safe])
              ->orWhereRaw("short_description {$like} ?", [$safe])
              ->orWhereRaw("sku {$like} ?", [$safe]);
        });
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('products.show', $this->slug);
    }

    public function getPrimaryImageAttribute(): ?ProductImage
    {
        return $this->images->first();
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedComparePriceAttribute(): ?string
    {
        return $this->compare_price
            ? '$' . number_format($this->compare_price, 2)
            : null;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getEffectiveMetaTitleAttribute(): string
    {
        return $this->meta_title ?? ($this->name . ' | Pacific Edge Labs');
    }

    public function getEffectiveMetaDescriptionAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }
        $source = $this->short_description ?? $this->description;
        return mb_strimwidth(strip_tags($source), 0, 155, '…');
    }
}
