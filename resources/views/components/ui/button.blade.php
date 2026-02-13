@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'type'     => 'button',
    'href'     => null,
    'disabled' => false,
    'icon'     => null,
    'iconEnd'  => null,
])

@php
$sizeClasses = match($size) {
    'sm'    => 'px-3.5 py-1.5 text-body-sm gap-1.5',
    'lg'    => 'px-6 py-3 text-body-lg gap-2.5',
    default => 'px-5 py-2.5 text-body gap-2',
};

$iconSize = match($size) {
    'sm'    => 'w-3.5 h-3.5',
    'lg'    => 'w-5 h-5',
    default => 'w-4 h-4',
};

// Filled variants (primary, secondary, danger) get active:scale-[0.98] press feedback.
// All variants get transition-smooth for consistent hover behaviour.
$variantClasses = match($variant) {
    'secondary' => 'bg-brand-navy text-white hover:bg-brand-navy-800 active:scale-[0.98] focus:ring-brand-navy',
    'outline'   => 'border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white focus:ring-brand-navy',
    'danger'    => 'bg-red-600 text-white hover:bg-red-700 active:scale-[0.98] focus:ring-red-600',
    'ghost'     => 'text-brand-navy hover:bg-brand-surface-2 focus:ring-brand-navy',
    default     => 'bg-brand-cyan text-white hover:bg-brand-cyan-dark active:scale-[0.98] focus:ring-brand-cyan',
};

$base = 'inline-flex items-center justify-center rounded-full font-medium cursor-pointer transition-smooth focus:outline-none focus:ring-2 focus:ring-offset-2';

$disabled       = (bool) $disabled;
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

$classes = trim("{$base} {$sizeClasses} {$variantClasses} {$disabledClasses}");
@endphp

@if($href)
    <a
        href="{{ $href }}"
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
        {{ $slot }}
        @if($iconEnd)
            <x-dynamic-component
                :component="'heroicon-o-' . $iconEnd"
                :class="$iconSize"
                aria-hidden="true"
            />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
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
        {{ $slot }}
        @if($iconEnd)
            <x-dynamic-component
                :component="'heroicon-o-' . $iconEnd"
                :class="$iconSize"
                aria-hidden="true"
            />
        @endif
    </button>
@endif
