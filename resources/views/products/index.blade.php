<x-app-layout>

    <x-ui.page-header
        title="All Research Compounds"
        subtitle="Browse our complete catalog of USA-tested peptides. Potency verified, purity quantified."
    />

    <x-ui.container class="mt-8 pb-16">

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap gap-3 mb-8 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="category-filter" class="block text-sm font-medium text-brand-text-muted mb-1">Category</label>
                <x-ui.form.select name="category" id="category-filter">
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
                <x-ui.button href="{{ route('products.index') }}" variant="outline" size="sm">
                    Clear
                </x-ui.button>
            @endif
        </form>

        {{-- Category Nav Pills --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('products.index') }}"
               @class(['px-4 py-1.5 text-sm rounded-full border transition',
                       'border-brand-cyan text-brand-cyan' => !$selected,
                       'border-zinc-700 text-zinc-400 hover:border-brand-cyan hover:text-brand-cyan' => $selected])>
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                   @class(['px-4 py-1.5 text-sm rounded-full border transition',
                           'border-brand-cyan text-brand-cyan' => $cat->slug === $selected,
                           'border-zinc-700 text-zinc-400 hover:border-brand-cyan hover:text-brand-cyan' => $cat->slug !== $selected])>
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Count --}}
        <p class="text-sm text-brand-text-muted mb-6">
            Showing {{ $products->count() }} {{ Str::plural('compound', $products->count()) }}
            @if($selected)
                in <span class="text-brand-navy font-medium">{{ $categories->firstWhere('slug', $selected)?->name }}</span>
            @endif
        </p>

        {{-- Grid --}}
        @if($products->isEmpty())
            <div class="text-center py-20 text-brand-text-muted">No compounds found.</div>
        @else
            <x-ui.grid>
                @foreach($products as $product)
                    <x-product.card
                        :name="$product->name"
                        :category="$product->category->name"
                        :price="$product->formatted_price"
                        :originalPrice="$product->formatted_compare_price"
                        :href="route('products.show', $product->slug)"
                        :imageSrc="$product->primary_image?->url"
                        :imageAlt="$product->primary_image?->alt_text ?? $product->name"
                        :researchSummary="$product->short_description"
                    />
                @endforeach
            </x-ui.grid>
        @endif

    </x-ui.container>

</x-app-layout>
