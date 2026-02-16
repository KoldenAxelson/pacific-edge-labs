# [TASK-2-007] Product Detail Page

## Overview
Build the public-facing product detail page at `/products/{slug}`. Single-page scroll
layout with anchor jump navigation between sections. This is the highest-priority SEO
page in Phase 2.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 3–4 hrs
**Depends on:** TASK-2-005
**Blocks:** TASK-2-012 (SEO builds on this template)

---

## Route

Add to `routes/web.php`:
```php
Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->name('products.show');
```

---

## Controller (`app/Http/Controllers/ProductController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('active', true)
            ->with(['category', 'images', 'researchLinks'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
```

---

## View (`resources/views/products/show.blade.php`)

Page sections in scroll order, each with an `id` for anchor nav:

1. `#overview` — image + name/price/badges/add-to-cart
2. `#specifications` — form, concentration, storage
3. `#description` — full research description
4. `#research` — cited studies (or "No research links yet" placeholder)
5. `#coa` — CoA placeholder (Phase 3)
6. `#disclaimer` — research-only disclaimer

```blade
<x-app-layout>
    <x-slot name="title">{{ $product->effective_meta_title }}</x-slot>
    <x-slot name="meta_description">{{ $product->effective_meta_description }}</x-slot>

    <x-ui.container class="py-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}" class="hover:text-brand-400">All Products</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-brand-400">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-zinc-300">{{ $product->name }}</span>
        </nav>

        {{-- Anchor Jump Nav --}}
        <div class="hidden md:flex gap-6 text-sm text-zinc-400 border-b border-zinc-800 mb-8 pb-2">
            @foreach(['overview' => 'Overview', 'specifications' => 'Specifications', 'description' => 'Description', 'research' => 'Research', 'coa' => 'CoA'] as $anchor => $label)
                <a href="#{{ $anchor }}" class="hover:text-brand-400 transition pb-2 border-b-2 border-transparent hover:border-brand-400">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- SECTION 1: Overview --}}
        <section id="overview" class="scroll-mt-20 grid md:grid-cols-2 gap-10 mb-16">

            {{-- Product Image --}}
            <div class="aspect-square bg-zinc-900 rounded-xl overflow-hidden flex items-center justify-center">
                @if($product->primaryImage)
                    <img
                        src="{{ $product->primaryImage->url }}"
                        alt="{{ $product->primaryImage->alt_text ?? $product->name }}"
                        class="w-full h-full object-contain p-8"
                    />
                @else
                    <div class="text-zinc-700 text-sm">No image available</div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex flex-col justify-center space-y-6">

                {{-- Category + Badges --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('categories.show', $product->category->slug) }}"
                       class="text-xs text-brand-400 uppercase tracking-widest hover:underline">
                        {{ $product->category->name }}
                    </a>
                    @if($product->is_on_sale)
                        <x-product.badge variant="sale">Sale</x-product.badge>
                    @endif
                    @if($product->featured)
                        <x-product.badge variant="featured">Featured</x-product.badge>
                    @endif
                </div>

                <h1 class="text-3xl font-bold text-white leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="text-sm text-zinc-500">SKU: {{ $product->sku }}</div>

                {{-- Price --}}
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-brand-400">
                        {{ $product->formatted_price }}
                    </span>
                    @if($product->is_on_sale)
                        <span class="text-lg text-zinc-500 line-through">
                            {{ $product->formatted_compare_price }}
                        </span>
                    @endif
                </div>

                {{-- Research Summary --}}
                @if($product->short_description)
                    <p class="text-zinc-400 text-sm leading-relaxed border-l-2 border-brand-400 pl-4">
                        {{ $product->short_description }}
                    </p>
                @endif

                {{-- Add to Cart (non-functional placeholder) --}}
                <x-ui.button variant="primary" size="lg" disabled class="w-full opacity-50 cursor-not-allowed" aria-label="Add to cart — available soon">
                    Add to Cart
                </x-ui.button>
                <p class="text-xs text-zinc-600 text-center">
                    Cart functionality coming soon
                </p>

            </div>
        </section>

        {{-- SECTION 2: Specifications --}}
        <x-ui.section id="specifications" title="Specifications" class="scroll-mt-20 mb-12">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                @foreach([
                    'Form' => $product->form,
                    'Content' => $product->concentration,
                    'Storage' => $product->storage_conditions,
                    'SKU' => $product->sku,
                ] as $label => $value)
                    @if($value)
                        <div>
                            <dt class="text-zinc-500 font-medium">{{ $label }}</dt>
                            <dd class="text-zinc-200 mt-0.5">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </x-ui.section>

        {{-- SECTION 3: Description --}}
        <x-ui.section id="description" title="Research Overview" class="scroll-mt-20 mb-12">
            <div class="prose prose-invert prose-sm max-w-none text-zinc-300 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </x-ui.section>

        {{-- SECTION 4: Research Links --}}
        <x-ui.section id="research" title="Cited Research" class="scroll-mt-20 mb-12">
            @if($product->researchLinks->isEmpty())
                <p class="text-zinc-600 text-sm italic">
                    Research citations for this compound will be added shortly.
                </p>
            @else
                <ol class="space-y-4">
                    @foreach($product->researchLinks as $i => $link)
                        <li class="text-sm text-zinc-400 border-l-2 border-zinc-700 pl-4">
                            <p class="text-zinc-200 font-medium">{{ $link->title }}</p>
                            <p class="text-zinc-500 mt-0.5">{{ $link->citation }}</p>
                            @if($link->journal)
                                <p class="text-zinc-600 italic">{{ $link->journal }}</p>
                            @endif
                            <a
                                href="{{ $link->pubmed_url ?? $link->url }}"
                                target="_blank" rel="noopener noreferrer"
                                class="text-brand-400 hover:underline text-xs mt-1 inline-block"
                            >
                                View Source →
                            </a>
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-ui.section>

        {{-- SECTION 5: CoA Placeholder (Phase 3) --}}
        <x-ui.section id="coa" title="Certificate of Analysis" class="scroll-mt-20 mb-12">
            <div class="border border-dashed border-zinc-700 rounded-lg p-8 text-center text-zinc-600 text-sm">
                <p class="font-medium text-zinc-500 mb-1">Batch CoA — Coming in Phase 3</p>
                <p>Each batch is independently tested. Certificate of Analysis documentation will be available here.</p>
            </div>
        </x-ui.section>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-xl font-semibold text-white mb-6">
                    More from {{ $product->category->name }}
                </h2>
                <x-ui.grid cols="4">
                    @foreach($relatedProducts as $related)
                        <x-product.card
                            :product="$related"
                            :researchSummary="$related->short_description"
                        />
                    @endforeach
                </x-ui.grid>
            </div>
        @endif

    </x-ui.container>

    {{-- Research disclaimer is already in app layout --}}
</x-app-layout>
```

---

## Acceptance Criteria
- [ ] `/products/semaglutide-15mg` returns 200 with correct name and price
- [ ] `/products/nonexistent-slug` returns 404
- [ ] Inactive product returns 404
- [ ] Breadcrumb renders correct category link
- [ ] Anchor jump nav links to correct page sections
- [ ] Primary image renders (or placeholder shows if no image)
- [ ] Sale price + strikethrough renders for BAC Water (`compare_price` is set)
- [ ] Research links render for Semaglutide (has seeded links)
- [ ] Research placeholder renders for products without links
- [ ] CoA placeholder section renders with Phase 3 note
- [ ] Related products grid shows up to 4 from same category
- [ ] "Add to Cart" button is rendered but visually disabled
- [ ] Page is responsive on mobile
