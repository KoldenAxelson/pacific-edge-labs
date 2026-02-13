@props([
    'label'    => null,
    'for'      => null,
    'error'    => null,
    'hint'     => null,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>

    @if($label)
        <label
            @if($for) for="{{ $for }}" @endif
            class="text-label font-medium text-brand-text"
        >
            {{ $label }}
            @if($required)
                <span class="text-brand-error ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p class="flex items-center gap-1.5 text-caption text-brand-error">
            <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" aria-hidden="true" />
            {{ $error }}
        </p>
    @elseif($hint)
        <p class="text-caption text-brand-text-muted">{{ $hint }}</p>
    @endif

</div>
