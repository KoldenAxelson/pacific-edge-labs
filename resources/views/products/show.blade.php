<x-app-layout>

    {{-- Mobile sub-nav for product sections (hidden on md+ where header nav takes over) --}}
    <div class="sticky top-16 z-30 md:hidden bg-white border-b border-brand-border overflow-x-auto">
        <nav class="flex items-center gap-1 px-4 py-2 min-w-max" aria-label="Page sections">
            @foreach(['overview' => 'Overview', 'specifications' => 'Specs', 'description' => 'Description', 'research' => 'Research', 'coa' => 'CoA'] as $anchor => $label)
                <a
                    href="#{{ $anchor }}"
                    class="px-2.5 py-1 text-body-sm font-medium text-brand-navy-600 hover:text-brand-cyan hover:bg-brand-surface-2 rounded-lg transition-smooth whitespace-nowrap"
                >{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    <x-ui.container class="pt-8 pb-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-brand-text-muted mb-6" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}" class="hover:text-brand-cyan transition">All Products</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-brand-cyan transition">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-brand-text">{{ $product->name }}</span>
        </nav>

        {{-- SECTION 1: Overview --}}
        <section id="overview" class="scroll-mt-28 md:scroll-mt-20 grid md:grid-cols-2 gap-10 mb-16">

            {{-- Product Image --}}
            <div class="aspect-square bg-brand-surface rounded-xl overflow-hidden flex items-center justify-center border border-brand-border">
                @if($product->primary_image)
                    <img
                        src="{{ $product->primary_image->url }}"
                        alt="{{ $product->primary_image->alt_text ?? $product->name }}"
                        class="w-full h-full object-contain p-8"
                    />
                @else
                    <div class="flex flex-col items-center gap-2 text-brand-text-faint">
                        <x-heroicon-o-beaker class="w-16 h-16" />
                        <span class="text-sm">No image available</span>
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex flex-col justify-center space-y-6">

                {{-- Category + Badges --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('categories.show', $product->category->slug) }}"
                       class="text-xs text-brand-cyan uppercase tracking-widest hover:underline">
                        {{ $product->category->name }}
                    </a>
                    @if($product->is_on_sale)
                        <x-product.badge variant="sale" />
                    @endif
                    @if($product->featured)
                        <x-product.badge variant="new" label="Featured" />
                    @endif
                </div>

                <h1 class="text-3xl font-bold text-brand-navy leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="text-sm text-brand-text-muted">SKU: {{ $product->sku }}</div>

                {{-- Price --}}
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-brand-cyan">
                        {{ $product->formatted_price }}
                    </span>
                    @if($product->is_on_sale)
                        <span class="text-lg text-brand-text-muted line-through">
                            {{ $product->formatted_compare_price }}
                        </span>
                    @endif
                </div>

                {{-- Research Summary --}}
                @if($product->short_description)
                    <p class="text-brand-text-muted text-sm leading-relaxed border-l-2 border-brand-cyan pl-4">
                        {{ $product->short_description }}
                    </p>
                @endif

                {{-- Add to Cart (non-functional placeholder) --}}
                <x-ui.button variant="primary" size="lg" :disabled="true" class="w-full">
                    Add to Cart
                </x-ui.button>
                <p class="text-xs text-brand-text-faint text-center">
                    Cart functionality coming soon
                </p>

            </div>
        </section>

        {{-- SECTION 2: Specifications --}}
        <section id="specifications" class="scroll-mt-28 md:scroll-mt-20 mb-12">
            <h2 class="text-xl font-semibold text-brand-navy mb-4">Specifications</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                @foreach([
                    'Form' => $product->form,
                    'Content' => $product->concentration,
                    'Storage' => $product->storage_conditions,
                    'SKU' => $product->sku,
                ] as $label => $value)
                    @if($value)
                        <div>
                            <dt class="text-brand-text-muted font-medium">{{ $label }}</dt>
                            <dd class="text-brand-navy mt-0.5">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </section>

        {{-- SECTION 3: Description --}}
        <section id="description" class="scroll-mt-28 md:scroll-mt-20 mb-12">
            <h2 class="text-xl font-semibold text-brand-navy mb-4">Research Overview</h2>
            <div class="prose prose-sm max-w-none text-brand-text-muted leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </section>

        {{-- SECTION 4: Research Links --}}
        <section id="research" class="scroll-mt-28 md:scroll-mt-20 mb-12">
            <h2 class="text-xl font-semibold text-brand-navy mb-4">Cited Research</h2>
            @if($product->researchLinks->isEmpty())
                <p class="text-brand-text-faint text-sm italic">
                    Research citations for this compound will be added shortly.
                </p>
            @else
                <ol class="space-y-4">
                    @foreach($product->researchLinks as $link)
                        <li class="text-sm text-brand-text-muted border-l-2 border-brand-border pl-4">
                            <p class="text-brand-navy font-medium">{{ $link->title }}</p>
                            <p class="text-brand-text-muted mt-0.5">{{ $link->citation }}</p>
                            @if($link->journal)
                                <p class="text-brand-text-faint italic">{{ $link->journal }}</p>
                            @endif
                            <a
                                href="{{ $link->pubmed_url ?? $link->url }}"
                                target="_blank" rel="noopener noreferrer"
                                class="text-brand-cyan hover:underline text-xs mt-1 inline-block"
                            >
                                View Source →
                            </a>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        {{-- SECTION 5: CoA Placeholder (Phase 3) --}}
        <section id="coa" class="scroll-mt-28 md:scroll-mt-20 mb-12">
            <h2 class="text-xl font-semibold text-brand-navy mb-4">Certificate of Analysis</h2>
            <div class="border border-dashed border-brand-border rounded-lg p-8 text-center text-brand-text-muted text-sm">
                <p class="font-medium text-brand-text-muted mb-1">Batch CoA — Coming in Phase 3</p>
                <p>Each batch is independently tested. Certificate of Analysis documentation will be available here.</p>
            </div>
        </section>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-16 mb-8">
                <h2 class="text-xl font-semibold text-brand-navy mb-6">
                    More from {{ $product->category->name }}
                </h2>
                <x-ui.grid cols="4">
                    @foreach($relatedProducts as $related)
                        <x-product.card
                            :name="$related->name"
                            :category="$related->category->name"
                            :price="$related->formatted_price"
                            :originalPrice="$related->formatted_compare_price"
                            :href="route('products.show', $related->slug)"
                            :imageSrc="$related->primary_image?->url"
                            :imageAlt="$related->primary_image?->alt_text ?? $related->name"
                            :researchTagline="$related->name"
                            :researchSummary="$related->short_description"
                        />
                    @endforeach
                </x-ui.grid>
            </div>
        @endif

    </x-ui.container>

</x-app-layout>
