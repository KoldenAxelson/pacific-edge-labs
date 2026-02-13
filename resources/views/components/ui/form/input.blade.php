@props([
    'error' => null,
])

@php
// Named slots that aren't passed are simply undefined — isset() handles this
// safely, but @elseisset is not a real Blade directive so we resolve both
// slot states here and use plain @if checks below.
$hasLeading  = isset($leading)  && (string) $leading  !== '';
$hasTrailing = isset($trailing) && (string) $trailing !== '';

$borderClass = $error
    ? 'border-brand-error focus:border-brand-error focus:ring-brand-error/20'
    : 'border-brand-border focus:border-brand-cyan focus:ring-brand-cyan/20';

$base = 'w-full rounded-xl border bg-white text-body-sm text-brand-text ' .
        'placeholder:text-brand-text-faint ' .
        'focus:ring-2 focus:outline-none ' .
        'transition-smooth ' .
        'disabled:opacity-50 disabled:cursor-not-allowed';

$paddingX = match(true) {
    $hasLeading && $hasTrailing => 'pl-10 pr-10',
    $hasLeading                 => 'pl-10 pr-4',
    $hasTrailing                => 'px-4 pr-10',
    default                     => 'px-4',
};

$classes = trim("{$base} py-2.5 {$paddingX} {$borderClass}");
@endphp

@if($hasLeading || $hasTrailing)
    <div class="relative flex items-center">
        @if($hasLeading)
            <div class="pointer-events-none absolute left-3 flex items-center text-brand-text-muted">
                {{ $leading }}
            </div>
        @endif

        <input {{ $attributes->merge(['class' => $classes]) }} />

        @if($hasTrailing)
            <div class="pointer-events-none absolute right-3 flex items-center text-brand-text-muted">
                {{ $trailing }}
            </div>
        @endif
    </div>
@else
    <input {{ $attributes->merge(['class' => $classes]) }} />
@endif
