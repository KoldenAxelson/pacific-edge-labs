@props([
    'variant'    => 'info',
    'title'      => null,
    'dismissible' => false,
])

@php
// info uses bg-brand-cyan-subtle — bg-brand-info-bg is not yet in the token set.
// If a brand-info-bg token is added in future, swap the info classes here.
$config = match($variant) {
    'success' => [
        'classes' => 'bg-brand-success-bg border-emerald-200 text-emerald-900',
        'icon'    => 'heroicon-o-check-circle',
    ],
    'warning' => [
        'classes' => 'bg-brand-warning-bg border-amber-200 text-amber-900',
        'icon'    => 'heroicon-o-exclamation-triangle',
    ],
    'error' => [
        'classes' => 'bg-brand-error-bg border-red-200 text-red-900',
        'icon'    => 'heroicon-o-x-circle',
    ],
    default => [  // info
        'classes' => 'bg-brand-cyan-subtle border-cyan-200 text-cyan-900',
        'icon'    => 'heroicon-o-information-circle',
    ],
};
@endphp

<div
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
    role="alert"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border px-4 py-3 ' . $config['classes']]) }}
>
    {{-- Semantic icon --}}
    <x-dynamic-component
        :component="$config['icon']"
        class="w-5 h-5 shrink-0 mt-0.5"
        aria-hidden="true"
    />

    {{-- Body --}}
    <div class="flex-1 min-w-0">
        @if($title)
            <p class="text-body-sm font-semibold mb-0.5">{{ $title }}</p>
        @endif
        <div class="text-body-sm">{{ $slot }}</div>
    </div>

    {{-- Dismiss button — only rendered when dismissible --}}
    @if($dismissible)
        <button
            type="button"
            @click="show = false"
            class="shrink-0 -mt-0.5 -mr-1 p-1 rounded hover:bg-black/10 transition-smooth"
            aria-label="Dismiss alert"
        >
            <x-heroicon-o-x-mark class="w-4 h-4" aria-hidden="true" />
        </button>
    @endif
</div>
