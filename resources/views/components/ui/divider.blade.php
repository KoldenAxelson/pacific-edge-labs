@props(['spacing' => 'default'])

@php
$spacingClass = match($spacing) {
    'tight'  => 'my-4',
    'loose'  => 'my-12',
    default  => 'my-8',
};
@endphp

<hr {{ $attributes->merge(['class' => "border-t border-brand-border {$spacingClass}"]) }}>
