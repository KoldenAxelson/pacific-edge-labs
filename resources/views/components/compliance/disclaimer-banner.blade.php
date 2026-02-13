{{--
  [TASK-1-010] Compliance Disclaimer Banner
  Persistent amber strip rendered on every page via app.blade.php.
  Props:
    variant  — 'page-top' (border-bottom) | 'footer' (border-top). Default: 'page-top'.
    compact  — bool. Compact omits the secondary explanatory sentence. Default: false.
--}}
@props([
    'variant' => 'page-top',
    'compact' => false,
])

@php
$borderClass = $variant === 'footer' ? 'border-t' : 'border-b';
@endphp

<div {{ $attributes->merge(['class' => "w-full {$borderClass} border-brand-amber-border bg-brand-amber-bg"]) }}>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">

            {{-- Beaker icon --}}
            <x-heroicon-o-beaker
                class="w-3.5 h-3.5 text-brand-amber flex-shrink-0"
                aria-hidden="true"
            />

            {{-- Label: amber small-caps --}}
            <span class="text-caption font-semibold uppercase tracking-widest text-brand-amber whitespace-nowrap">
                Research Use Only
            </span>

            {{-- Visual separator --}}
            <span class="text-brand-amber/40 hidden sm:inline select-none" aria-hidden="true">·</span>

            {{-- Disclaimer copy --}}
            <p class="text-caption text-brand-amber/80">
                All products sold strictly for laboratory research. Not for human consumption.
                @unless($compact)
                    These products are intended exclusively for qualified research professionals
                    and institutions.
                @endunless
            </p>

        </div>
    </div>
</div>
