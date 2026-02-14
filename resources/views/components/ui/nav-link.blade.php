{{--
  [TASK-1-012] Nav Link Component
  Props:
    href    – destination URL
    active  – bool, explicitly set active state.
               If omitted, auto-detected by comparing href to the current URL path.
  Usage (desktop):
    <x-ui.nav-link href="/">Home</x-ui.nav-link>
  Usage (mobile – pass layout classes via $attributes):
    (Not used directly; mobile links are plain <a> tags in navigation.blade.php
     to avoid CSS specificity collisions on display/padding overrides.)
--}}
@props([
    'href'   => '#',
    'active' => null,
])

@php
// Auto-detect active state from href when not explicitly passed.
// Strip leading slash from path for request()->is() compatibility.
$path    = trim(parse_url($href, PHP_URL_PATH) ?? '', '/');
$isActive = $active ?? (
    $path === ''
        ? request()->is('/')
        : request()->is($path) || request()->is($path . '/*')
);

$stateClasses = $isActive
    ? 'text-brand-cyan bg-brand-cyan-subtle'
    : 'text-brand-navy-700 hover:bg-brand-surface-2 hover:text-brand-navy';
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'flex items-center px-3 py-1.5 rounded-lg text-body-sm font-medium transition-smooth ' . $stateClasses]) }}
    @if($isActive) aria-current="page" @endif
>
    {{ $slot }}
</a>
