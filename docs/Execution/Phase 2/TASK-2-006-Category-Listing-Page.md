# [TASK-2-006] Category Listing Page

## Overview
Build the public-facing category page at `/categories/{slug}`. Displays the category
hero, intro text, and all active products in that category using the existing
`<x-product.card>` component.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-005
**Blocks:** Nothing (parallel with TASK-2-007 and TASK-2-008)

---

## Route

Add to `routes/web.php`:
```php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/categories/{slug}', [CategoryController::class, 'show'])
    ->name('categories.show');
```

---

## Controller (`app/Http/Controllers/CategoryController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $products = $category->products()
            ->active()
            ->with('images')
            ->orderBy('name')
            ->get();

        $categories = Category::active()->ordered()->get(); // for nav/sidebar

        return view('categories.show', compact('category', 'products', 'categories'));
    }
}
```

---

## View (`resources/views/categories/show.blade.php`)

```blade
<x-app-layout>
    <x-slot name="title">{{ $category->effective_meta_title ?? $category->name . ' | Pacific Edge Labs' }}</x-slot>

    {{-- Category Hero --}}
    <x-ui.page-header
        :title="$category->hero_title ?? $category->name"
        :subtitle="$category->description"
    />

    <x-ui.container>

        {{-- Category Nav Pills --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('products.index') }}"
               class="px-4 py-1.5 text-sm rounded-full border border-zinc-700 text-zinc-400 hover:border-brand-400 hover:text-brand-400 transition">
                All Products
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   @class([
                       'px-4 py-1.5 text-sm rounded-full border transition',
                       'border-brand-400 text-brand-400' => $cat->id === $category->id,
                       'border-zinc-700 text-zinc-400 hover:border-brand-400 hover:text-brand-400' => $cat->id !== $category->id,
                   ])>
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Product Count --}}
        <p class="text-sm text-zinc-500 mb-6">
            {{ $products->count() }} {{ Str::plural('compound', $products->count()) }}
        </p>

        {{-- Product Grid --}}
        @if($products->isEmpty())
            <div class="text-center py-20 text-zinc-500">
                No active products in this category.
            </div>
        @else
            <x-ui.grid>
                @foreach($products as $product)
                    <x-product.card
                        :product="$product"
                        :researchSummary="$product->short_description"
                    />
                @endforeach
            </x-ui.grid>
        @endif

    </x-ui.container>
</x-app-layout>
```

---

## Acceptance Criteria
- [ ] `/categories/glp-1-metabolic` returns 200 with 6 product cards
- [ ] `/categories/sexual-health` returns 200 with 3 product cards
- [ ] `/categories/nonexistent-slug` returns 404
- [ ] Hero title and description render from the category model
- [ ] Active category nav pill is highlighted
- [ ] Product count is accurate
- [ ] `<x-product.card>` `researchSummary` prop is populated
- [ ] Page is responsive on mobile
