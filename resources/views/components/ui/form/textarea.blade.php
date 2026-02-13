@props([
    'error' => null,
    'rows'  => 4,
])

@php
$borderClass = $error
    ? 'border-brand-error focus:border-brand-error focus:ring-brand-error/20'
    : 'border-brand-border focus:border-brand-cyan focus:ring-brand-cyan/20';

$base = 'w-full rounded-xl border bg-white px-4 py-2.5 text-body-sm text-brand-text
         placeholder:text-brand-text-faint
         focus:ring-2 focus:outline-none
         resize-y
         transition-smooth
         disabled:opacity-50 disabled:cursor-not-allowed';

$classes = trim(preg_replace('/\s+/', ' ', "{$base} {$borderClass}"));
@endphp

<textarea
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => $classes]) }}
></textarea>
