<x-app-layout>

    {{-- Category Hero --}}
    <x-ui.page-header
        :title="$category->hero_title ?? $category->name"
        :subtitle="$category->description"
    />

    <x-ui.container class="pb-10">

        {{-- Category Nav Pills --}}
        <div class="flex flex-wrap gap-2 mb-8 mt-8">
            {{-- "All Products" link — route wired in TASK-2-008 --}}
            <a href="{{ route('products.index') }}"
               class="px-4 py-1.5 text-sm rounded-full border border-zinc-700 text-zinc-400 hover:border-brand-cyan hover:text-brand-cyan transition">
                All Products
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   @class([
                       'px-4 py-1.5 text-sm rounded-full border transition',
                       'border-brand-cyan text-brand-cyan' => $cat->id === $category->id,
                       'border-zinc-700 text-zinc-400 hover:border-brand-cyan hover:text-brand-cyan' => $cat->id !== $category->id,
                   ])>
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Product Count --}}
        <p class="text-sm text-brand-text-muted mb-6">
            {{ $products->count() }} {{ Str::plural('compound', $products->count()) }}
        </p>

        {{-- Product Grid --}}
        @if($products->isEmpty())
            <div class="text-center py-20 text-brand-text-muted">
                No active products in this category.
            </div>
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
