@props(['cols' => 3])

@php
$colClass = match((int) $cols) {
    2       => 'grid-cols-1 sm:grid-cols-2',
    4       => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
};
@endphp

<div {{ $attributes->merge(['class' => "grid gap-6 {$colClass}"]) }}>
    {{ $slot }}
</div>
