@props(['size' => 'default'])

@php
$sizeClass = match($size) {
    'sm'   => 'max-w-3xl',
    'lg'   => 'max-w-7xl',
    'full' => 'max-w-none',
    default => 'max-w-6xl',
};
@endphp

<div {{ $attributes->merge(['class' => "mx-auto w-full px-4 sm:px-6 lg:px-8 {$sizeClass}"]) }}>
    {{ $slot }}
</div>
