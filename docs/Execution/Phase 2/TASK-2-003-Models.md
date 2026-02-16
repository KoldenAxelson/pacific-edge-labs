# [TASK-2-003] Product & Category Models

## Overview
Build the Eloquent models for Category, Product, ProductImage, and ProductResearchLink.
Include relationships, scopes, accessors, and casts. Also create the model factories.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-002
**Blocks:** TASK-2-004, TASK-2-006, TASK-2-007

---

## Files to Create

```
app/Models/
├── Category.php              ← new
├── Product.php               ← new
├── ProductImage.php          ← new
└── ProductResearchLink.php   ← new

database/factories/
├── CategoryFactory.php       ← new
└── ProductFactory.php        ← new
```

---

## Category Model (`app/Models/Category.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'name', 'slug', 'description',
        'hero_title', 'sort_order', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }

    public function getActiveProductCountAttribute(): int
    {
        return $this->products()->active()->count();
    }
}
```

---

## Product Model (`app/Models/Product.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use SoftDeletes;

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
        return $query->where(function ($q) use ($safe) {
            $q->whereILike('name', $safe)
              ->orWhereILike('description', $safe)
              ->orWhereILike('short_description', $safe)
              ->orWhereILike('sku', $safe);
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
```

---

## ProductImage Model (`app/Models/ProductImage.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'path', 'alt_text', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        // Reads from S3 in production, local storage in dev
        return Storage::url($this->path);
    }

    public function getIsPrimaryAttribute(): bool
    {
        return $this->sort_order === 0;
    }
}
```

---

## ProductResearchLink Model (`app/Models/ProductResearchLink.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductResearchLink extends Model
{
    protected $fillable = [
        'product_id', 'title', 'authors', 'publication_year',
        'journal', 'pubmed_id', 'url', 'sort_order',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'sort_order'       => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPubmedUrlAttribute(): ?string
    {
        return $this->pubmed_id
            ? 'https://pubmed.ncbi.nlm.nih.gov/' . $this->pubmed_id
            : null;
    }

    /**
     * Short citation string for display: "Smith J et al. (2021)"
     */
    public function getCitationAttribute(): string
    {
        $parts = [];
        if ($this->authors) $parts[] = $this->authors;
        if ($this->publication_year) $parts[] = "({$this->publication_year})";
        return implode(' ', $parts) ?: $this->title;
    }
}
```

---

## Factories

### `database/factories/CategoryFactory.php`
```php
public function definition(): array
{
    $name = $this->faker->unique()->randomElement([
        'GLP-1 & Metabolic', 'Recovery & Healing',
        'Performance & Growth', 'Cognitive & Longevity',
        'Sexual Health', 'Ancillaries',
    ]);

    return [
        'parent_id'   => null,
        'name'        => $name,
        'slug'        => Str::slug($name),
        'description' => $this->faker->paragraph(),
        'hero_title'  => null,
        'sort_order'  => $this->faker->numberBetween(1, 10),
        'active'      => true,
    ];
}
```

### `database/factories/ProductFactory.php`
```php
public function definition(): array
{
    $name = $this->faker->unique()->words(3, true);

    return [
        'category_id'       => Category::factory(),
        'sku'               => 'PEL-' . strtoupper($this->faker->lexify('???-##')),
        'name'              => ucwords($name),
        'slug'              => Str::slug($name),
        'short_description' => $this->faker->sentence(12),
        'description'       => $this->faker->paragraphs(3, true),
        'form'              => 'Lyophilized powder',
        'concentration'     => $this->faker->randomElement(['5mg per vial', '10mg per vial', '15mg per vial']),
        'storage_conditions'=> 'Store in a cool, dry environment and protect from direct light.',
        'price'             => $this->faker->randomFloat(2, 29.99, 149.99),
        'compare_price'     => null,
        'featured'          => false,
        'active'            => true,
    ];
}

public function onSale(): static
{
    return $this->state(fn (array $attrs) => [
        'compare_price' => $attrs['price'] * 1.25,
    ]);
}

public function inactive(): static
{
    return $this->state(['active' => false]);
}
```

---

## Quick Smoke Test (Tinker)

```php
// Confirm relationships resolve
$p = App\Models\Product::with(['category', 'images', 'researchLinks'])->first();
$p->category->name;
$p->formatted_price;
$p->effective_meta_title;

// Confirm scopes
App\Models\Product::active()->inCategory('glp-1-metabolic')->count();
App\Models\Product::search('semaglutide')->count();
```

---

## Acceptance Criteria
- [ ] All four models created with correct fillable, casts, relationships, scopes, and accessors
- [ ] Factories created and can generate valid records
- [ ] `Product::active()->count()` returns correct results
- [ ] `Product::search('sema')` returns matching products
- [ ] `$product->effective_meta_title` falls back to name when `meta_title` is null
- [ ] `$product->effective_meta_description` truncates to 155 chars
- [ ] `$image->url` resolves via `Storage::url()`
- [ ] `$link->pubmed_url` returns correct PubMed URL when `pubmed_id` is set
