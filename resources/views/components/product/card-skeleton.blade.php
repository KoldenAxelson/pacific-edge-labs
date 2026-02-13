{{--
    card-skeleton.blade.php
    Pulse placeholder that mirrors card.blade.php's exact structure and dimensions.
    Used while products load in Phase 2. No logic — layout only.
--}}

<div {{ $attributes->merge(['class' => 'flex flex-col bg-brand-surface rounded-xl border border-brand-border overflow-hidden animate-pulse']) }}>

    {{-- Image area — matches aspect-[4/3], with corner badge placeholder --}}
    <div class="relative aspect-[4/3] bg-brand-surface-2">
        <div class="absolute top-2 right-2 h-5 w-12 bg-brand-border rounded-full"></div>
    </div>

    {{-- Card body --}}
    <div class="flex flex-col flex-1 p-4 gap-3">

        {{-- Category eyebrow --}}
        <div class="h-3 w-16 bg-brand-surface-2 rounded"></div>

        {{-- Product name — two lines to match line-clamp-2 worst case --}}
        <div class="space-y-2 -mt-1">
            <div class="h-5 w-full bg-brand-surface-2 rounded"></div>
            <div class="h-5 w-3/4 bg-brand-surface-2 rounded"></div>
        </div>

        {{-- Badge group — purity + batch --}}
        <div class="flex gap-1.5">
            <div class="h-6 w-14 bg-brand-surface-2 rounded-full"></div>
            <div class="h-6 w-24 bg-brand-surface-2 rounded-full"></div>
        </div>

    </div>

    {{-- Card footer --}}
    <div class="px-4 py-3 border-t border-brand-border flex items-center justify-between gap-3">
        <div class="h-6 w-14 bg-brand-surface-2 rounded"></div>
        <div class="h-8 w-28 bg-brand-surface-2 rounded-full"></div>
    </div>

</div>
