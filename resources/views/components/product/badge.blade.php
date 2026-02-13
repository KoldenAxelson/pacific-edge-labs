@props([
    'variant' => 'research',
    'value'   => null,
    'label'   => null,
    'size'    => 'sm',
])

@php
$sizeClasses = match($size) {
    'xs'    => 'px-2 py-0.5 text-caption',
    default => 'px-2.5 py-1 text-body-sm',
};

$iconSize = match($size) {
    'xs'    => 'w-3 h-3',
    default => 'w-3.5 h-3.5',
};

// Each variant owns its complete style string — no appending, no conflicts.
$config = match($variant) {
    'purity' => [
        'classes' => 'bg-brand-cyan-subtle border border-brand-cyan-light text-brand-cyan',
        'icon'    => 'heroicon-o-check-badge',
        'text'    => $label ?? $value,
        'mono'    => true,
    ],
    'batch' => [
        'classes' => 'bg-brand-surface-2 border border-brand-border text-brand-text-muted',
        'icon'    => null,
        'text'    => $label ?? $value,
        'mono'    => true,
    ],
    'in_stock' => [
        'classes' => 'bg-emerald-50 border border-emerald-200 text-emerald-700',
        'icon'    => 'heroicon-o-check-circle',
        'text'    => $label ?? 'In Stock',
        'mono'    => false,
    ],
    'low_stock' => [
        'classes' => 'bg-brand-amber-bg border border-brand-amber-border text-brand-amber',
        'icon'    => 'heroicon-o-exclamation-triangle',
        'text'    => $label ?? 'Low Stock',
        'mono'    => false,
    ],
    'out_of_stock' => [
        'classes' => 'bg-slate-100 border border-slate-300 text-slate-500',
        'icon'    => 'heroicon-o-x-circle',
        'text'    => $label ?? 'Out of Stock',
        'mono'    => false,
    ],
    'category' => [
        'classes' => 'bg-white border border-brand-navy text-brand-navy',
        'icon'    => null,
        'text'    => $label ?? $value,
        'mono'    => false,
    ],
    'new' => [
        'classes' => 'bg-brand-cyan border border-brand-cyan text-white',
        'icon'    => null,
        'text'    => $label ?? 'New',
        'mono'    => false,
    ],
    default => [
        // research — amber treatment, same palette as compliance UI
        'classes' => 'bg-brand-amber-bg border border-brand-amber-border text-brand-amber',
        'icon'    => 'heroicon-o-beaker',
        'text'    => $label ?? 'Research Only',
        'mono'    => false,
    ],
};

$base    = 'inline-flex items-center gap-1 rounded-full font-medium';
$classes = "{$base} {$sizeClasses} {$config['classes']}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($config['icon'])
        <x-dynamic-component
            :component="$config['icon']"
            :class="$iconSize"
            aria-hidden="true"
        />
    @endif

    @if($config['mono'])
        <span class="font-mono-data">{{ $config['text'] }}</span>
    @else
        {{ $config['text'] }}
    @endif
</span>
