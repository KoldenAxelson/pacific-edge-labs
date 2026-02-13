@props([
    'label'       => null,
    'description' => null,
    'error'       => null,
    'compliance'  => false,
])

@php
$checkboxClasses = 'h-4 w-4 rounded border-brand-border text-brand-cyan cursor-pointer
                    focus:ring-2 focus:ring-brand-cyan/20 focus:ring-offset-0
                    disabled:opacity-50 disabled:cursor-not-allowed';

$checkboxClasses = trim(preg_replace('/\s+/', ' ', $checkboxClasses));
@endphp

@if($compliance)
    {{-- Compliance mode — amber background signals this is a meaningful attestation --}}
    <div class="rounded-xl border border-brand-amber-border bg-brand-amber-bg p-4">
        <div class="flex items-start gap-3">
            <input
                type="checkbox"
                @if($attributes->has('id')) @else id="{{ 'checkbox-' . uniqid() }}" @endif
                {{ $attributes->merge(['class' => $checkboxClasses]) }}
            />
            <div class="flex flex-col gap-1 min-w-0">
                @if($label)
                    <label
                        @if($attributes->has('id')) for="{{ $attributes->get('id') }}" @endif
                        class="text-body-sm font-semibold text-brand-amber cursor-pointer leading-snug"
                    >
                        {{ $label }}
                    </label>
                @endif
                @if($description)
                    <p class="text-caption text-brand-text-muted leading-relaxed">{{ $description }}</p>
                @endif
                @if($error)
                    <p class="flex items-center gap-1.5 text-caption text-brand-error mt-1">
                        <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" aria-hidden="true" />
                        {{ $error }}
                    </p>
                @endif
            </div>
        </div>
    </div>
@else
    {{-- Standard mode --}}
    <div class="flex flex-col gap-1.5">
        <div class="flex items-start gap-3">
            <input
                type="checkbox"
                {{ $attributes->merge(['class' => $checkboxClasses]) }}
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
        @if($error)
            <p class="flex items-center gap-1.5 text-caption text-brand-error">
                <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" aria-hidden="true" />
                {{ $error }}
            </p>
        @endif
    </div>
@endif
