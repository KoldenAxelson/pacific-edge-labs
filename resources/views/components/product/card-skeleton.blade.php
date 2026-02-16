{{--
    card-skeleton.blade.php
    Pulse placeholder that mirrors card.blade.php's exact structure and dimensions.
    Updated for the reworked card: square image, image bottom bar (price + CTA),
    top-right badge, and single content zone with purity badge only.

    No overflow-hidden on the outer wrapper (matching card.blade.php) — safe here
    because skeletons never render a tooltip.
--}}

<div {{ $attributes->merge(['class' => 'flex flex-col bg-brand-surface rounded-xl border border-brand-border overflow-hidden animate-pulse']) }}>

    {{-- Image area — matches aspect-square + rounded-t-xl --}}
    <div class="relative aspect-square bg-brand-surface-2 rounded-t-xl">

        {{-- Top right: status badge placeholder --}}
        <div class="absolute top-2 right-2 h-5 w-14 bg-brand-border rounded-full"></div>

        {{-- Bottom bar: price left, CTA right --}}
        <div class="absolute bottom-0 left-0 right-0 flex items-end justify-between p-2">
            <div class="h-7 w-12 bg-brand-border rounded-md"></div>
            <div class="h-9 w-9 bg-brand-border rounded-full"></div>
        </div>

    </div>

    {{-- Content zone --}}
    <div class="flex flex-col p-3 gap-1.5">

        {{-- Category eyebrow --}}
        <div class="h-3 w-16 bg-brand-surface-2 rounded"></div>

        {{-- Product name — two lines to match line-clamp-2 worst case --}}
        <div class="space-y-1.5">
            <div class="h-4 w-full bg-brand-surface-2 rounded"></div>
            <div class="h-4 w-3/4 bg-brand-surface-2 rounded"></div>
        </div>

        {{-- Purity badge --}}
        <div class="h-5 w-14 bg-brand-surface-2 rounded-full"></div>

    </div>

</div>
