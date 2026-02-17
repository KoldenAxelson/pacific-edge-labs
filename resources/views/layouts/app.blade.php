<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Pacific Edge Labs | Premium Research Peptides' }}</title>

        {{-- SEO meta tags (description, canonical, OG, Twitter) --}}
        @yield('meta')
        {{ $meta ?? '' }}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    {{--
      x-data on <body> provides the mobileOpen scope for navigation.blade.php.

      Why here and not in navigation.blade.php:
        navigation.blade.php contains the sticky <header> (sticky top-0 z-40),
        the fixed backdrop, and the fixed sidebar. If all three were wrapped in a
        short <div x-data>, the sticky element's parent would only be ~90px tall
        and sticky would stop working after scrolling past the announcement bar.
        Putting x-data on <body> gives the sticky header a full-page-height parent,
        so it correctly sticks to the top of the viewport throughout the page.

      <body> does not set position or z-index, so it does NOT create a stacking
      context. The fixed backdrop (z-40) and sidebar (z-50) participate in the
      root stacking context at their own z values and are not trapped.
    --}}
    <body class="font-body bg-brand-bg text-brand-navy antialiased" x-data="{
        mobileOpen: false,
        searchOpen: false,
        searchQuery: '',
        searchResults: [],
        searchCount: 0,
        searchLoading: false,
        searchSearched: false,
        searchTrayOpen: false,
        _searchTimer: null,
        openSearch() {
            this.searchOpen = true;
            this.$nextTick(() => document.getElementById('nav-search-input')?.focus());
        },
        closeSearch() {
            this.searchOpen = false;
            this.searchTrayOpen = false;
            this.searchQuery = '';
            this.searchResults = [];
            this.searchCount = 0;
            this.searchSearched = false;
            this.searchLoading = false;
            clearTimeout(this._searchTimer);
        },
        doSearch() {
            clearTimeout(this._searchTimer);
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.searchResults = [];
                this.searchCount = 0;
                this.searchSearched = false;
                this.searchTrayOpen = false;
                this.searchLoading = false;
                return;
            }
            this.searchLoading = true;
            this.searchTrayOpen = false;
            this._searchTimer = setTimeout(() => {
                fetch('/search?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        this.searchResults = data.results;
                        this.searchCount = data.count;
                        this.searchSearched = true;
                        this.searchLoading = false;
                        this.$nextTick(() => { this.searchTrayOpen = true; });
                    })
                    .catch(() => { this.searchLoading = false; });
            }, 300);
        }
    }">

        {{-- Navigation: announcement bar + sticky header + backdrop + sidebar --}}
        @include('layouts.navigation')

        {{-- Compliance disclaimer — always rendered on every page --}}
        <x-compliance.disclaimer-banner variant="page-top" />

        {{-- Page-level header slot --}}
        @isset($header)
            {{ $header }}
        @endisset

        <main>
            {{-- Flash messages (success / error / warning / info / validation errors) --}}
            <x-ui.flash-messages />

            {{ $slot }}
        </main>

        {{-- Site footer --}}
        <x-ui.footer />

        {{-- Toast container — window._showToast() API, fixed bottom-right z-60 --}}
        <x-ui.toast-container />

        {{-- Age verification gate --}}
        <x-compliance.age-gate />

        {{-- Schema.org JSON-LD structured data --}}
        @stack('schema')

    </body>
</html>
