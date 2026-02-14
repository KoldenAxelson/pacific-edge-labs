{{--
  [TASK-1-012 rev.2] Site Header
  Architecture notes:
  
  Alpine scope: x-data="{ mobileOpen: false }" lives on <body> in app.blade.php.
  This file does NOT define its own x-data wrapper.
  Reason: wrapping <header sticky> in a short div breaks sticky — the header can
  only stick within its parent's height. With x-data on <body>, the header's parent
  is <body> which spans the full page, and sticky works correctly across the whole page.
  
  Backdrop and sidebar are fixed elements and siblings of <header> within <body>.
  They are NOT children of <header>, so they are not trapped inside header's z-40
  stacking context. They participate in the root stacking context at their own z values.

  Nav design: hamburger-only. No always-visible center nav links.
  Desktop center nav block has been removed. Hamburger is visible at all screen sizes.
  All nav links live exclusively in the sidebar.

  Z-index convention:
    z-30  secondary sticky subnav
    z-40  main site header + backdrop
    z-50  sidebar, modals, age gate
    z-60  toast notifications
--}}

{{-- ── Announcement bar ────────────────────────────────────────────────────
     Not sticky. Scrolls away — header covers it on scroll.
--}}
<div class="w-full bg-brand-navy">
    <p class="text-center text-caption py-2 px-4">
        <span class="text-white">Free U.S. shipping on orders $150+</span>
        <span class="mx-2 text-brand-cyan" aria-hidden="true">&bull;</span>
        <span class="text-white">Free international shipping on orders $500+</span>
    </p>
</div>

{{-- ── Sticky header ──────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-40 bg-white border-b border-brand-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Left: Wordmark --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0" aria-label="Pacific Edge Labs — home">
                <div class="w-8 h-8 bg-brand-navy rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-beaker class="w-4 h-4 text-brand-cyan" aria-hidden="true" />
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-heading font-semibold text-brand-navy text-base leading-tight">Pacific Edge</span>
                    <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-brand-navy-500 leading-tight mt-0.5">Labs</span>
                </div>
            </a>

            {{-- Right: Actions + Hamburger --}}
            <div class="flex items-center gap-0.5">

                {{-- Search --}}
                <button
                    type="button"
                    class="p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Search"
                >
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" aria-hidden="true" />
                </button>

                {{-- Cart with hardcoded 0 badge (Phase 4: dynamic) --}}
                <button
                    type="button"
                    class="relative p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Shopping cart, 0 items"
                >
                    <x-heroicon-o-shopping-cart class="w-5 h-5" aria-hidden="true" />
                    <span
                        class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-cyan text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none pointer-events-none"
                        aria-hidden="true"
                    >0</span>
                </button>

                {{-- Auth buttons — desktop: always visible --}}
                @auth
                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 ml-1 px-3 py-1.5 rounded-lg hover:bg-brand-surface-2 transition-smooth"
                        aria-label="Your profile"
                    >
                        <div
                            class="w-7 h-7 rounded-full bg-brand-navy flex items-center justify-center text-white text-caption font-semibold flex-shrink-0"
                            aria-hidden="true"
                        >{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <span class="text-body-sm font-medium text-brand-navy-700 hidden sm:block">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                @else
                    <div class="hidden sm:flex items-center gap-2 ml-1">
                        <a
                            href="{{ route('login') }}"
                            class="px-3 py-1.5 text-body-sm font-medium text-brand-navy-700 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                        >Log in</a>
                        <a
                            href="{{ route('register') }}"
                            class="px-3 py-1.5 text-body-sm font-medium text-white bg-brand-navy hover:bg-brand-navy-800 rounded-lg transition-smooth"
                        >Register</a>
                    </div>
                @endauth

                {{--
                  Morphing hamburger — visible at ALL screen sizes.
                  .hamburger CSS in app.css handles span transforms only.
                  display/flex-direction/gap are Tailwind utilities so nothing
                  in .hamburger fights with responsive display utilities.
                  :class="{ 'active': mobileOpen }" triggers CSS morph to X.
                --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    :class="{ 'active': mobileOpen }"
                    class="hamburger flex flex-col gap-1.5 ml-1 p-1.5 text-brand-navy hover:text-brand-cyan transition-smooth"
                    :aria-expanded="mobileOpen.toString()"
                    aria-label="Toggle navigation menu"
                    aria-controls="mobile-nav"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

            </div>
            {{-- /Right --}}

        </div>
    </div>
</header>
{{-- /Sticky header --}}


{{-- ── Backdrop ──────────────────────────────────────────────────────────────
     Fixed full-screen. z-40 — same layer as header. Renders after header in DOM
     so sits visually on top when open. Clicking closes the sidebar.
     This element is a sibling of <header> within <body>, so it is NOT inside
     header's stacking context. It participates in the root stacking context.
--}}
<div
    x-show="mobileOpen"
    x-cloak
    x-transition:enter="transition-smooth"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-smooth"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="mobileOpen = false"
    class="fixed inset-0 bg-black/40 z-40"
    aria-hidden="true"
></div>


{{-- ── Sidebar ────────────────────────────────────────────────────────────────
     Fixed right-side panel. z-50 — above backdrop and header.
     Sibling of <header> within <body>: not trapped in header's stacking context.
--}}
<div
    id="mobile-nav"
    x-show="mobileOpen"
    x-cloak
    x-transition:enter="transition-medium"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition-medium"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    @keydown.escape.window="mobileOpen = false"
    class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl z-50 overflow-y-auto flex flex-col"
    aria-label="Site navigation"
>
    {{-- Sidebar header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-brand-border flex-shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-brand-navy rounded-md flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-beaker class="w-3.5 h-3.5 text-brand-cyan" aria-hidden="true" />
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-heading font-semibold text-brand-navy text-sm leading-tight">Pacific Edge</span>
                <span class="font-mono text-[9px] tracking-[0.2em] uppercase text-brand-navy-500 leading-tight mt-0.5">Labs</span>
            </div>
        </div>
        <button
            type="button"
            @click="mobileOpen = false"
            class="p-1.5 text-brand-navy-500 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
            aria-label="Close navigation"
        >
            <x-heroicon-o-x-mark class="w-5 h-5" aria-hidden="true" />
        </button>
    </div>

    {{-- Nav links --}}
    @php
        $link   = 'flex items-center px-4 py-2.5 rounded-xl text-body-sm font-medium transition-smooth';
        $active = 'text-brand-cyan bg-brand-cyan-subtle';
        $rest   = 'text-brand-navy-700 hover:bg-brand-surface-2 hover:text-brand-navy';
    @endphp

    <nav class="flex-1 px-3 py-3 space-y-0.5" aria-label="Navigation links">
        <a
            href="/"
            class="{{ $link }} {{ request()->is('/') ? $active : $rest }}"
            @if(request()->is('/')) aria-current="page" @endif
            @click="mobileOpen = false"
        >Home</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">Products</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">About</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">FAQ</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">Contact</a>
    </nav>

    {{-- Auth section --}}
    <div class="px-3 py-3 border-t border-brand-border flex-shrink-0">
        @auth
            <div class="flex items-center gap-3 px-4 py-2 mb-1">
                <div
                    class="w-8 h-8 rounded-full bg-brand-navy flex items-center justify-center text-white text-body-sm font-semibold flex-shrink-0"
                    aria-hidden="true"
                >{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <p class="text-body-sm font-medium text-brand-navy truncate">{{ Auth::user()->name }}</p>
                    <p class="text-caption text-brand-text-muted truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="{{ $link }} {{ $rest }} w-full text-left">Log Out</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">Log in</a>
            <a href="{{ route('register') }}" class="{{ $link }} text-white bg-brand-navy hover:bg-brand-navy-800 mt-1 text-center" @click="mobileOpen = false">Register</a>
        @endauth
    </div>

</div>
{{-- /Sidebar --}}
