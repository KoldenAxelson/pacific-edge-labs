@props([
    'title' => 'Certificates of Analysis',
])

<div {{ $attributes }}>

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2.5 mb-1.5">
        <x-heroicon-o-shield-check class="w-5 h-5 text-brand-cyan flex-shrink-0" />
        <h3 class="text-h3 text-brand-navy">{{ $title }}</h3>
    </div>

    {{-- Trust statement --}}
    <p class="text-body-sm text-brand-text-muted mb-5">
        Third-party tested for purity and potency. Full documentation available for every batch.
    </p>

    {{-- ── Accordion items ─────────────────────────────────────────────── --}}
    <div class="space-y-2">
        {{ $slot }}
    </div>

</div>
