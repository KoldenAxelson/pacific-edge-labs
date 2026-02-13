@props([
    'icon'     => null,
    'variant'  => 'ghost',
    'size'     => 'md',
    'label'    => '',
    'href'     => null,
    'disabled' => false,
])

@php
// All sizes meet the 44px minimum touch target requirement.
// The visual icon grows with size; the button container stays ≥ 44px.
$sizeClasses = match($size) {
    'sm'    => 'w-11 h-11',   // 44px — minimum touch target
    'lg'    => 'w-14 h-14',   // 56px
    default => 'w-11 h-11',   // 44px — standard
};

$iconSize = match($size) {
    'sm'    => 'w-4 h-4',
    'lg'    => 'w-6 h-6',
    default => 'w-5 h-5',
};

// Only ghost/primary/danger variants are valid for icon-only buttons.
$variantClasses = match($variant) {
    'primary' => 'bg-brand-cyan text-white hover:bg-brand-cyan-dark active:scale-[0.98] focus:ring-brand-cyan',
    'danger'  => 'bg-red-600 text-white hover:bg-red-700 active:scale-[0.98] focus:ring-red-600',
    default   => 'text-brand-navy hover:bg-brand-surface-2 focus:ring-brand-navy',
};

$base = 'inline-flex items-center justify-center rounded-full cursor-pointer transition-smooth focus:outline-none focus:ring-2 focus:ring-offset-2 shrink-0';

$disabled        = (bool) $disabled;
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

$classes = trim("{$base} {$sizeClasses} {$variantClasses} {$disabledClasses}");
@endphp

@if($href)
    <a
        href="{{ $href }}"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        @if($icon)
            <x-dynamic-component
                :component="'heroicon-o-' . $icon"
                :class="$iconSize"
                aria-hidden="true"
            />
        @endif
    </a>
@else
    <button
        type="button"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) disabled @endif
    >
        @if($icon)
            <x-dynamic-component
                :component="'heroicon-o-' . $icon"
                :class="$iconSize"
                aria-hidden="true"
            />
        @endif
    </button>
@endif
