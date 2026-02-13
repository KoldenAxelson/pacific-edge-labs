@props([
    'variant'  => 'info',
    'message'  => '',
    'duration' => 4000,
])

@php
// Dark navy background so toasts stand out against the off-white page surface.
// Variant controls the accent icon color only — base card is always brand-navy.
$config = match($variant) {
    'success' => [
        'icon'      => 'heroicon-o-check-circle',
        'iconClass' => 'text-emerald-400',
    ],
    'warning' => [
        'icon'      => 'heroicon-o-exclamation-triangle',
        'iconClass' => 'text-amber-400',
    ],
    'error' => [
        'icon'      => 'heroicon-o-x-circle',
        'iconClass' => 'text-red-400',
    ],
    default => [  // info
        'icon'      => 'heroicon-o-information-circle',
        'iconClass' => 'text-cyan-400',
    ],
};
@endphp

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ (int) $duration }})"
    x-show="show"
    x-transition:enter="animate-reveal-bottom"
    x-transition:leave="transition-medium"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    role="status"
    aria-live="polite"
    {{ $attributes->merge(['class' => 'flex items-center gap-3 bg-brand-navy border border-brand-navy-700 text-white rounded-lg px-4 py-3 shadow-lg pointer-events-auto w-full']) }}
>
    <x-dynamic-component
        :component="$config['icon']"
        class="w-5 h-5 shrink-0 {{ $config['iconClass'] }}"
        aria-hidden="true"
    />
    <p class="text-body-sm flex-1 min-w-0">{{ $message }}</p>
</div>
