# [TASK-2-009] Search Functionality

## Overview
Implement real-time product search using Livewire with 300ms debounce. Search is
accessible from a persistent icon in the nav header that expands to an input, and
results appear on a dedicated `/search` page.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-008
**Blocks:** Nothing

---

## Files to Create / Modify

```
app/Livewire/
└── ProductSearch.php                    ← new

resources/views/
├── livewire/
│   └── product-search.blade.php         ← new
└── search.blade.php                     ← new (search results page)
```

Modify:
```
resources/views/components/
└── [navigation/header component]        ← add search trigger icon
```

---

## Route

```php
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
```

---

## Livewire Component (`app/Livewire/ProductSearch.php`)

```php
<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Url;

class ProductSearch extends Component
{
    #[Url(as: 'q')]
    public string $query = '';

    public function updatedQuery(): void
    {
        // Triggered by wire:model.live.debounce.300ms
        // URL updates via #[Url] — no manual dispatch needed
    }

    public function render()
    {
        $results = collect();

        if (strlen(trim($this->query)) >= 2) {
            $results = Product::active()
                ->search($this->query)
                ->with('images', 'category')
                ->limit(30)
                ->get();
        }

        return view('livewire.product-search', [
            'results'    => $results,
            'hasQuery'   => strlen(trim($this->query)) >= 2,
            'totalCount' => $results->count(),
        ]);
    }
}
```

---

## Livewire View (`resources/views/livewire/product-search.blade.php`)

```blade
<div>
    {{-- Search Input --}}
    <div class="relative mb-8">
        <x-ui.form.input
            wire:model.live.debounce.300ms="query"
            type="search"
            placeholder="Search compounds, SKUs, or descriptions..."
            class="w-full pl-10"
            autofocus
        />
        <svg class="absolute left-3 top-3.5 w-4 h-4 text-zinc-500" .../>
        @if($query)
            <button wire:click="$set('query', '')" class="absolute right-3 top-3 text-zinc-500 hover:text-white">
                ✕
            </button>
        @endif
    </div>

    {{-- Loading State --}}
    <div wire:loading class="text-sm text-zinc-500 text-center py-4">
        Searching...
    </div>

    {{-- Results --}}
    <div wire:loading.remove>
        @if($hasQuery)
            <p class="text-sm text-zinc-500 mb-6">
                {{ $totalCount }} {{ Str::plural('result', $totalCount) }} for
                <span class="text-zinc-300">"{{ $query }}"</span>
            </p>

            @if($results->isEmpty())
                <div class="text-center py-16 text-zinc-600">
                    <p class="text-lg mb-2">No compounds matched "{{ $query }}"</p>
                    <p class="text-sm">Try a different name, SKU, or mechanism.</p>
                </div>
            @else
                <x-ui.grid>
                    @foreach($results as $product)
                        <x-product.card
                            :product="$product"
                            :researchSummary="$product->short_description"
                        />
                    @endforeach
                </x-ui.grid>
            @endif
        @else
            {{-- Empty state: show category shortcuts --}}
            <div class="text-center py-12 text-zinc-600 text-sm">
                Enter at least 2 characters to search
            </div>
        @endif
    </div>
</div>
```

---

## Search Results Page (`resources/views/search.blade.php`)

```blade
<x-app-layout>
    <x-slot name="title">Search | Pacific Edge Labs</x-slot>

    <x-ui.page-header title="Search" subtitle="Search our full catalog of research compounds." />

    <x-ui.container>
        <livewire:product-search />
    </x-ui.container>
</x-app-layout>
```

---

## Header Search Trigger (modify navigation component)

Add to the nav — clicking the icon navigates to `/search` and autofocuses the Livewire input:

```blade
<a href="{{ route('products.search') }}" aria-label="Search products"
   class="text-zinc-400 hover:text-brand-400 transition p-2 rounded-md">
    <svg class="w-5 h-5" ...><!-- search icon SVG --></svg>
</a>
```

---

## Controller method (add to `ProductController`)

```php
public function search(): View
{
    return view('search');
}
```

---

## Acceptance Criteria
- [ ] `/search` page renders with search input
- [ ] Typing "sema" returns Semaglutide within 300ms debounce
- [ ] Typing "GLP" returns GLP-1 category products
- [ ] Typing a SKU ("PEL-SEM-15") returns the matching product
- [ ] Empty query shows "enter 2 characters" prompt (not an empty grid)
- [ ] Zero results shows "no compounds matched" message
- [ ] Search icon in nav header is present and links to `/search`
- [ ] URL updates to `/search?q=sema` as the user types (via Livewire `#[Url]`)
- [ ] Bookmarking `/search?q=sema` loads with query pre-filled
- [ ] Loading indicator appears during debounce
