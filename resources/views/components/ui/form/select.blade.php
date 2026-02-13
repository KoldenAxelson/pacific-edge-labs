@props([
    'error' => null,
])

@php
$borderClass = $error
    ? 'border-brand-error focus:border-brand-error focus:ring-brand-error/20'
    : 'border-brand-border focus:border-brand-cyan focus:ring-brand-cyan/20';

$base = 'w-full rounded-xl border bg-white px-4 py-2.5 pr-10 text-body-sm text-brand-text
         appearance-none
         focus:ring-2 focus:outline-none
         transition-smooth
         disabled:opacity-50 disabled:cursor-not-allowed';

$classes = trim(preg_replace('/\s+/', ' ', "{$base} {$borderClass}"));
@endphp

<div class="relative">
    <select {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
        <x-heroicon-o-chevron-down class="h-4 w-4 text-brand-text-muted" aria-hidden="true" />
    </div>
</div>
