@props(['spacing' => 'default'])

@php
$spacingClass = match($spacing) {
    'tight'  => 'py-8',
    'loose'  => 'py-16 md:py-24',
    'hero'   => 'py-20 md:py-32',
    default  => 'py-12 md:py-16',
};
@endphp

<section {{ $attributes->merge(['class' => $spacingClass]) }}>
    {{ $slot }}
</section>
