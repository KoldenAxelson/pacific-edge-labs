@props(['gap' => 3])

<div {{ $attributes->merge(['class' => "flex flex-wrap items-center gap-{$gap}"]) }}>
    {{ $slot }}
</div>
