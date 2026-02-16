# [TASK-2-008] "All Products" Page with Filters

## Overview
Build the `/products` page showing all active products with category filter dropdown.
This is the main catalog landing page.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-005
**Blocks:** TASK-2-009 (search builds on this page)

---

## Route

```php
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
```

---

## Controller method (add to `ProductController`)

```php
public function index(Request $request): View
{
    $query = Product::active()->with('images')->orderBy('name');

    if ($request->filled('category')) {
        $query->inCategory($request->string('category')->toString());
    }

    if ($request->filled('q')) {
        $query->search($request->string('q')->toString());
    }

    $products   = $query->get();
    $categories = Category::active()->ordered()->get();
    $selected   = $request->input('category', '');

    return view('products.index', compact('products', 'categories', 'selected'));
}
```

---

## View (`resources/views/products/index.blade.php`)

```blade
<x-app-layout>
    <x-slot name="title">All Research Peptides | Pacific Edge Labs</x-slot>

    <x-ui.page-header
        title="All Research Compounds"
        subtitle="Browse our complete catalog of USA-tested peptides. Potency verified, purity quantified."
    />

    <x-ui.container>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap gap-3 mb-8 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-ui.form.select name="category" label="Category" :value="$selected">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected($cat->slug === $selected)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-ui.form.select>
            </div>
            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
            @if($selected)
                <a href="{{ route('products.index') }}" class="text-sm text-zinc-500 hover:text-brand-400 transition self-center">
                    Clear
                </a>
            @endif
        </form>

        {{-- Category Nav Pills --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('products.index') }}"
               @class(['px-4 py-1.5 text-sm rounded-full border transition',
                       'border-brand-400 text-brand-400' => !$selected,
                       'border-zinc-700 text-zinc-400 hover:border-brand-400 hover:text-brand-400' => $selected])>
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                   @class(['px-4 py-1.5 text-sm rounded-full border transition',
                           'border-brand-400 text-brand-400' => $cat->slug === $selected,
                           'border-zinc-700 text-zinc-400 hover:border-brand-400 hover:text-brand-400' => $cat->slug !== $selected])>
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Count --}}
        <p class="text-sm text-zinc-500 mb-6">
            Showing {{ $products->count() }} {{ Str::plural('compound', $products->count()) }}
            @if($selected) in <span class="text-zinc-300">{{ $categories->firstWhere('slug', $selected)?->name }}</span> @endif
        </p>

        {{-- Grid --}}
        @if($products->isEmpty())
            <div class="text-center py-20 text-zinc-500">No compounds found.</div>
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
- [ ] `/products` shows all 30 active products
- [ ] `/products?category=glp-1-metabolic` shows 6 products
- [ ] `/products?category=sexual-health` shows 3 products
- [ ] Category filter dropdown defaults to selected value on load
- [ ] Nav pills highlight the active filter
- [ ] Product count text updates correctly when filtered
- [ ] "Clear" link appears when a filter is active and removes it
- [ ] Page is responsive on mobile
