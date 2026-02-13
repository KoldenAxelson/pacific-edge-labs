@props([
    'label'       => null,
    'description' => null,
])

@php
$radioClasses = 'h-4 w-4 border-brand-border text-brand-cyan cursor-pointer
                 focus:ring-2 focus:ring-brand-cyan/20 focus:ring-offset-0
                 disabled:opacity-50 disabled:cursor-not-allowed';

$radioClasses = trim(preg_replace('/\s+/', ' ', $radioClasses));
@endphp

<div class="flex items-start gap-3">
    <input
        type="radio"
        {{ $attributes->merge(['class' => $radioClasses]) }}
    />
    <div class="flex flex-col gap-0.5 min-w-0">
        @if($label)
            <label
                @if($attributes->has('id')) for="{{ $attributes->get('id') }}" @endif
                class="text-body-sm font-medium text-brand-text cursor-pointer leading-snug"
            >
                {{ $label }}
            </label>
        @endif
        @if($description)
            <p class="text-caption text-brand-text-muted">{{ $description }}</p>
        @endif
    </div>
</div>
