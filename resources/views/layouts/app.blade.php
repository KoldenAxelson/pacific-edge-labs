<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body bg-brand-bg text-brand-navy antialiased">

        {{-- Primary navigation --}}
        @include('layouts.navigation')

        {{-- Compliance disclaimer banner — opt-in per page via named slot.
             Product pages pass: <x-slot name="banner"><x-compliance.disclaimer-banner /></x-slot>
             Pages that omit the slot show nothing. --}}
        @isset($banner)
            {{ $banner }}
        @endisset

        {{-- Page-level header (x-ui.page-header goes here via named slot) --}}
        @isset($header)
            {{ $header }}
        @endisset

        <main>
            {{-- Flash messages (success / error / warning / info / validation errors) --}}
            <x-ui.flash-messages />

            {{ $slot }}
        </main>

        {{-- Footer slot — populated in a later task --}}
        @isset($footer)
            {{ $footer }}
        @endisset

        {{-- Toast container — receives toasts from window._showToast() and Livewire (Phase 4/5) --}}
        <x-ui.toast-container />

        {{-- Age verification gate — full-viewport Alpine overlay, always shows in Phase 1 demo --}}
        {{-- Phase 4: replace `verified: false` with cookie/session persistence --}}
        <x-compliance.age-gate />

    </body>
</html>
