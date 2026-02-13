@props([
    'name'            => '',
    'category'        => '',
    'price'           => '',
    'originalPrice'   => null,
    'purity'          => '',
    'batchNumber'     => null,
    'batchStatus'     => 'in_stock',
    'href'            => '#',
    'imageSrc'        => null,
    'imageAlt'        => null,
    'researchSummary' => null,
])

@php
// Corner badge: sale takes priority over stock warnings. In stock shows nothing.
$cornerVariant = null;
if ($originalPrice) {
    $cornerVariant = 'sale';
} elseif ($batchStatus === 'low_stock') {
    $cornerVariant = 'low_stock';
} elseif ($batchStatus === 'out_of_stock') {
    $cornerVariant = 'out_of_stock';
}
@endphp

<div
    x-data="{ hovered: false }"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
    {{ $attributes->merge(['class' => 'group flex flex-col bg-brand-surface rounded-xl border border-brand-border hover:border-brand-cyan transition-smooth overflow-hidden']) }}
>
    {{-- â”€â”€ IMAGE AREA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-brand-surface-2">

        @if($imageSrc)
            <img
                src="{{ $imageSrc }}"
                alt="{{ $imageAlt ?? $name }}"
                :class="{ 'blur-sm brightness-50': hovered }"
                class="w-full h-full object-cover transition-smooth"
            />
        @else
            <div
                :class="{ 'blur-sm brightness-50': hovered }"
                class="w-full h-full flex items-center justify-center transition-smooth"
            >
                <x-heroicon-o-beaker class="w-16 h-16 text-brand-text-faint" />
            </div>
        @endif

        {{-- Research summary hover overlay â€” only rendered when prop is provided --}}
        @if($researchSummary)
            <div
                x-show="hovered"
                x-cloak
                x-transition:enter="transition-smooth"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-smooth"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 flex items-center justify-center p-5"
            >
                <p class="text-white text-body-sm text-center font-medium leading-relaxed">
                    {{ $researchSummary }}
                </p>
            </div>
        @endif

        {{-- Corner status badge â€” sale, low stock, or out of stock only. Nothing if in stock. --}}
        @if($cornerVariant)
            <div class="absolute top-2 right-2">
                <x-product.badge :variant="$cornerVariant" size="xs" />
            </div>
        @endif

    </div>

    {{-- â”€â”€ CARD BODY â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="flex flex-col flex-1 p-4 gap-3">

        {{-- Category eyebrow --}}
        <p class="text-caption font-medium tracking-widest uppercase text-brand-text-muted">
            {{ $category }}
        </p>

        {{-- Product name --}}
        <h4 class="text-h4 text-brand-navy leading-snug line-clamp-2 -mt-1">
            {{ $name }}
        </h4>

        {{-- Purity + Batch badges --}}
        <x-product.badge-group gap="gap-1.5">
            <x-product.badge variant="purity" :value="$purity" />
            @if($batchNumber)
                <x-product.badge variant="batch" :value="$batchNumber" />
            @endif
        </x-product.badge-group>

    </div>

    {{-- â”€â”€ CARD FOOTER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="px-4 py-3 border-t border-brand-border flex items-center justify-between gap-3">

        {{-- Price â€” stacks to two lines on sale, single line otherwise --}}
        @if($originalPrice)
            <div class="flex flex-col gap-0.5">
                <span class="font-mono-data text-brand-text-muted line-through leading-none">{{ $originalPrice }}</span>
                <span class="text-h4 text-brand-success font-semibold leading-none">{{ $price }}</span>
            </div>
        @else
            <span class="text-h4 text-brand-navy font-semibold leading-none">{{ $price }}</span>
        @endif

        {{-- CTA â€” disabled ghost for out of stock, primary link otherwise --}}
        @if($batchStatus === 'out_of_stock')
            <x-ui.button variant="ghost" size="sm" :disabled="true">
                Notify Me
            </x-ui.button>
        @else
            <x-ui.button variant="primary" size="sm" :href="$href" icon-end="arrow-right">
                View Product
            </x-ui.button>
        @endif

    </div>

</div>
