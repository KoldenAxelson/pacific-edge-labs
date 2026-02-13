@props([
    'gap' => 'gap-2',
])

<div {{ $attributes->merge(['class' => "flex flex-wrap items-center {$gap}"]) }}>
    {{ $slot }}
</div>
