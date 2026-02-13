@props([
    'batchNumber' => '',
    'testDate'    => '',
    'purity'      => '',
    'lab'         => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-5 gap-y-2 bg-brand-cyan-subtle border border-brand-cyan-light rounded-xl px-4 py-3']) }}>

    {{-- Shield icon --}}
    <x-heroicon-o-shield-check class="w-4 h-4 text-brand-cyan flex-shrink-0" aria-hidden="true" />

    {{-- Purity --}}
    <span class="text-body-sm text-brand-text-muted">
        Purity: <span class="font-mono-data text-brand-cyan">{{ $purity }}</span>
    </span>

    {{-- Batch --}}
    <span class="text-body-sm text-brand-text-muted">
        Batch: <span class="font-mono-data text-brand-navy">{{ $batchNumber }}</span>
    </span>

    {{-- Test Date --}}
    <span class="text-body-sm text-brand-text-muted">
        Tested: <span class="font-mono-data text-brand-navy">{{ $testDate }}</span>
    </span>

    {{-- Lab --}}
    <span class="text-body-sm text-brand-text-muted">
        Lab: <span class="font-mono-data text-brand-navy">{{ $lab }}</span>
    </span>

</div>
