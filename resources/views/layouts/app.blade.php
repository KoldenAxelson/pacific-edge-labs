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

        {{-- Primary navigation (sticky header, wordmark, mobile panel) --}}
        @include('layouts.navigation')

        {{-- Compliance disclaimer — always rendered on every page (page-top variant) --}}
        {{-- Phase 1 requirement: persistent amber strip. Not opt-in. --}}
        <x-compliance.disclaimer-banner variant="page-top" />

        {{-- Page-level header (x-ui.page-header goes here via named slot) --}}
        @isset($header)
            {{ $header }}
        @endisset

        <main>
            {{-- Flash messages (success / error / warning / info / validation errors) --}}
            <x-ui.flash-messages />

            {{ $slot }}
        </main>

        {{-- Site footer — navy background, 4-column grid, legal links --}}
        <x-ui.footer />

        {{-- Toast container — window._showToast() API, fixed bottom-right z-60 --}}
        <x-ui.toast-container />

        {{-- Age verification gate — full-viewport Alpine overlay --}}
        {{-- Phase 4: replace `verified: false` with cookie/session persistence --}}
        <x-compliance.age-gate />

    </body>
</html>
