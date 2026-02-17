@props([
    'name'            => '',
    'category'        => '',
    'price'           => '',
    'originalPrice'   => null,
    'purity'          => '',
    'batchStatus'     => 'in_stock',
    'href'            => '#',
    'imageSrc'        => null,
    'imageAlt'        => null,
    'researchTagline' => null,
    'researchSummary' => null,
])

@php
// Sale state is communicated via the price pills on the image — no corner badge needed.
// Corner badge is only for stock status warnings.
$cornerVariant = null;
if ($batchStatus === 'low_stock') {
    $cornerVariant = 'low_stock';
} elseif ($batchStatus === 'out_of_stock') {
    $cornerVariant = 'out_of_stock';
}
@endphp

<a
    href="{{ $href }}"
    x-data="{ hovered: false, notified: false, canHover: window.matchMedia('(min-width: 768px)').matches }"
    x-init="window.matchMedia('(min-width: 768px)').addEventListener('change', e => canHover = e.matches)"
    @mouseenter="if (canHover) hovered = true"
    @mouseleave="hovered = false"
    {{ $attributes->merge(['class' => 'group flex flex-col bg-brand-surface rounded-xl border border-brand-border hover:border-brand-cyan transition-smooth overflow-hidden cursor-pointer']) }}
>

    {{-- ── IMAGE ─────────────────────────────────────────────────────────── --}}
    <div class="relative aspect-square overflow-hidden bg-brand-surface-2 rounded-t-xl">

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
                <x-heroicon-o-beaker class="w-12 h-12 text-brand-text-faint" />
            </div>
        @endif

        {{-- Research overlay — fades in from above the blurred image on hover.     --}}
        {{-- Enter: 500ms ease-out, slides down from -8px. Feel is deliberate.      --}}
        {{-- Leave: 300ms ease-in, slides back up. Crisp exit, not a mirror.        --}}
        @if($researchTagline || $researchSummary)
            <div
                x-show="hovered"
                x-cloak
                x-transition:enter="transition duration-500 ease-out"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition duration-300 ease-in"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-5"
            >
                @if($researchTagline)
                    <p class="text-brand-navy font-semibold text-body-sm text-center leading-snug">
                        {{ $researchTagline }}
                    </p>
                @endif
                @if($researchSummary)
                    <p class="text-brand-navy-700 text-body-sm text-center leading-relaxed">
                        {{ $researchSummary }}
                    </p>
                @endif
            </div>
        @endif

        {{-- Status badge — fades out when hover overlay is active so text isn't competing. --}}
        @if($cornerVariant)
            <div
                :class="{ 'opacity-0': hovered }"
                class="absolute top-2 right-2 z-10 transition-smooth"
            >
                <x-product.badge :variant="$cornerVariant" size="xs" />
            </div>
        @endif

        {{-- ── IMAGE BOTTOM BAR: price left · CTA right ─────────────────── --}}
        <div class="absolute bottom-0 left-0 right-0 flex items-end justify-between gap-2 p-2 z-10">

            {{--
                Price badge(s):
                  • Regular  — cyan pill, mono-data, no bold (mono already reads as distinct)
                  • On sale  — two stacked pills: faded struck original, then green sale price.
                              The visual treatment IS the sale signal. No separate Sale badge needed.
            --}}
            <div class="flex flex-col gap-1 items-start">
                @if($originalPrice)
                    <span class="bg-brand-navy/60 text-white/60 rounded-md px-2 py-0.5 font-mono-data text-caption line-through leading-none backdrop-blur-sm">
                        {{ $originalPrice }}
                    </span>
                    <span class="bg-brand-success text-white rounded-md px-2 py-1 font-mono-data text-body-sm leading-none">
                        {{ $price }}
                    </span>
                @else
                    <span class="bg-brand-cyan text-white rounded-md px-2.5 py-1 font-mono-data text-body-sm leading-none">
                        {{ $price }}
                    </span>
                @endif
            </div>

            {{--
                CTA icon button — intentionally NOT using x-ui.button here.
                A pure icon button needs explicit sizing (w-10 h-10) to hit the 40px
                touch target without the text-padding assumptions of the button component.

                Add to cart: bg-brand-cyan, matching primary button variant.
                  Wire via Livewire dispatch or Alpine @click at the usage site.

                Out of stock: toggles between two states via Alpine `notified`.
                  Not notified → outline bell (border-2 navy), invites a click.
                  Notified     → filled green with a check, confirms opt-in.
                  Clicking again toggles back — the user owns the state locally
                  until a Livewire persist layer is wired in Phase N.
            --}}
            @if($batchStatus === 'out_of_stock')
                <button
                    type="button"
                    @click.prevent.stop="notified = !notified"
                    :aria-label="notified ? 'Cancel notification for {{ $name }}' : 'Notify me when {{ $name }} is back in stock'"
                    :class="notified
                        ? 'bg-brand-success text-white hover:bg-brand-success/85'
                        : 'border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white'"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full transition-smooth"
                >
                    <x-heroicon-o-check x-show="notified" class="w-4.5 h-4.5" />
                    <x-heroicon-o-bell x-show="!notified" class="w-4.5 h-4.5" />
                </button>
            @else
                <button
                    type="button"
                    @click.prevent.stop
                    aria-label="Add {{ $name }} to cart"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-brand-cyan hover:bg-brand-cyan-dark text-white transition-smooth"
                >
                    <x-heroicon-o-shopping-cart class="w-4.5 h-4.5" />
                </button>
            @endif

        </div>
    </div>

    {{-- ── CONTENT ────────────────────────────────────────────────────────── --}}
    {{-- Single padding zone, no border-t separator. --}}
    <div class="flex flex-col p-3 gap-1.5">

        <p class="text-caption font-medium tracking-widest uppercase text-brand-text-muted">
            {{ $category }}
        </p>

        <h4 class="text-body font-semibold text-brand-navy leading-snug line-clamp-2">
            {{ $name }}
        </h4>

        {{-- Purity — trust signal, sits where ratings would be on a standard e-commerce card. --}}
        @if($purity)
            <x-product.badge variant="purity" :value="$purity" size="xs" />
        @endif

    </div>

</a>
