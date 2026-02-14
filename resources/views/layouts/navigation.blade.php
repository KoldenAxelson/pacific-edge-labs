{{--
  [TASK-1-012] Site Header
  Replaces Breeze's default navigation.blade.php entirely.

  Structure:
    <header x-data="{ mobileOpen: false }">
      ├── Left:   wordmark / logo link
      ├── Center: desktop nav links (hidden md:hidden → md:flex)
      ├── Right:  search | cart badge | auth (login+register guest / avatar chip authed)
      │           hamburger (md:hidden)
      └── Mobile panel: absolute drop-down below header, full-width

  Z-index convention (established here, used across all Phase 1 tasks):
    z-30  secondary / sticky subnav (design page anchor nav, etc.)
    z-40  main site header  ← this element
    z-50  modals, overlays, age gate
    z-60  toast notifications

  Cart badge: hardcoded 0 for Phase 1. Phase 4 makes it dynamic.

  Mobile nav:
    - Trigger: hamburger button (@click toggles mobileOpen)
    - Dismiss: @click.outside + @keydown.escape.window
    - Enter animation: animate-reveal-bottom (CSS keyframe, not CSS transition)
    - Leave animation: opacity-0 via x-transition:leave-end
    - Auth section separated by a divider below nav links

  nav-link component (<x-ui.nav-link>) used for desktop center only.
  Mobile links are plain <a> tags with inline request()->is() active detection
  to avoid CSS specificity collisions when overriding display/padding classes.
--}}
<header
    x-data="{ mobileOpen: false }"
    class="sticky top-0 z-40 bg-white border-b border-brand-border"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- ── Left: Wordmark ───────────────────────────────────────── --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0" aria-label="Pacific Edge Labs — home">
                {{-- Beaker icon: small navy rounded-lg box, cyan icon --}}
                <div class="w-8 h-8 bg-brand-navy rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-beaker class="w-4 h-4 text-brand-cyan" aria-hidden="true" />
                </div>
                {{-- Wordmark: "Pacific Edge" DM Sans semibold / "Labs" mono --}}
                <div class="flex flex-col leading-none">
                    <span class="font-heading font-semibold text-brand-navy text-base leading-tight">Pacific Edge</span>
                    <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-brand-navy-500 leading-tight mt-0.5">Labs</span>
                </div>
            </a>

            {{-- ── Center: Desktop nav links ────────────────────────────── --}}
            <nav class="hidden md:flex items-center gap-0.5" aria-label="Main navigation">
                <x-ui.nav-link href="/">Home</x-ui.nav-link>
                <x-ui.nav-link href="#">Products</x-ui.nav-link>
                <x-ui.nav-link href="#">About</x-ui.nav-link>
                <x-ui.nav-link href="#">FAQ</x-ui.nav-link>
                <x-ui.nav-link href="#">Contact</x-ui.nav-link>
            </nav>

            {{-- ── Right: Actions ───────────────────────────────────────── --}}
            <div class="flex items-center gap-0.5">

                {{-- Search button --}}
                <button
                    type="button"
                    class="p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Search"
                >
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" aria-hidden="true" />
                </button>

                {{-- Cart button with hardcoded badge (Phase 4 makes dynamic) --}}
                <button
                    type="button"
                    class="relative p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Shopping cart, 0 items"
                >
                    <x-heroicon-o-shopping-cart class="w-5 h-5" aria-hidden="true" />
                    {{-- Cyan badge: -top-0.5 -right-0.5 as specified --}}
                    <span
                        class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-cyan text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none pointer-events-none"
                        aria-hidden="true"
                    >0</span>
                </button>

                {{-- Auth: avatar chip (authed) | login + register (guest) — desktop only --}}
                @auth
                    <a
                        href="{{ route('profile.edit') }}"
                        class="hidden md:flex items-center gap-2 ml-1 px-3 py-1.5 rounded-lg hover:bg-brand-surface-2 transition-smooth"
                        aria-label="Your profile"
                    >
                        <div
                            class="w-7 h-7 rounded-full bg-brand-navy flex items-center justify-center text-white text-caption font-semibold flex-shrink-0"
                            aria-hidden="true"
                        >{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <span class="text-body-sm font-medium text-brand-navy-700 hidden lg:block">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                @else
                    <div class="hidden md:flex items-center gap-2 ml-1">
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

                {{-- Hamburger — mobile only (md:hidden) --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden ml-1 p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    :aria-expanded="mobileOpen.toString()"
                    aria-label="Toggle navigation menu"
                    aria-controls="mobile-nav"
                >
                    {{-- Bars icon: visible when closed --}}
                    <x-heroicon-o-bars-3 x-show="!mobileOpen" class="w-5 h-5" aria-hidden="true" />
                    {{-- X icon: visible when open (x-cloak hides until Alpine boots) --}}
                    <x-heroicon-o-x-mark x-show="mobileOpen" x-cloak class="w-5 h-5" aria-hidden="true" />
                </button>

            </div>
            {{-- /Right --}}

        </div>
    </div>

    {{-- ── Mobile nav panel ─────────────────────────────────────────────── --}}
    {{--
      Positioned absolute, drops below header. Enter: animate-reveal-bottom (keyframe).
      Leave: transition-medium + opacity-0 fade.
      @click.outside fires on document clicks outside this element:
        — clicking the hamburger button (outside panel) fires click.outside
          BUT the button's @click also fires; both set mobileOpen=false, no conflict.
        — clicking a nav link inside the panel does NOT fire click.outside.
    --}}
    <div
        id="mobile-nav"
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="animate-reveal-bottom"
        x-transition:leave="transition-medium"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.outside="mobileOpen = false"
        @keydown.escape.window="mobileOpen = false"
        class="absolute top-full inset-x-0 bg-white border-b border-brand-border shadow-lg md:hidden"
        aria-label="Mobile navigation"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">

            {{-- Nav links — block, rounded-xl, hover bg as specified --}}
            <nav class="space-y-0.5" aria-label="Mobile navigation links">
                @php
                    $mobileLinkBase = 'flex w-full items-center px-4 py-2.5 rounded-xl text-body-sm font-medium transition-smooth';
                    $mobileActive   = 'text-brand-cyan bg-brand-cyan-subtle';
                    $mobileRest     = 'text-brand-navy-700 hover:bg-brand-surface-2 hover:text-brand-navy';
                @endphp

                <a
                    href="/"
                    class="{{ $mobileLinkBase }} {{ request()->is('/') ? $mobileActive : $mobileRest }}"
                    @if(request()->is('/')) aria-current="page" @endif
                    @click="mobileOpen = false"
                >Home</a>

                <a
                    href="#"
                    class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                    @click="mobileOpen = false"
                >Products</a>

                <a
                    href="#"
                    class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                    @click="mobileOpen = false"
                >About</a>

                <a
                    href="#"
                    class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                    @click="mobileOpen = false"
                >FAQ</a>

                <a
                    href="#"
                    class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                    @click="mobileOpen = false"
                >Contact</a>
            </nav>

            {{-- Divider + auth section --}}
            <div class="mt-3 pt-3 border-t border-brand-border">
                @auth
                    {{-- User info chip --}}
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
                    <a
                        href="{{ route('profile.edit') }}"
                        class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                        @click="mobileOpen = false"
                    >Profile</a>
                    {{-- Logout via POST form --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="{{ $mobileLinkBase }} {{ $mobileRest }} text-left"
                        >Log Out</button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="{{ $mobileLinkBase }} {{ $mobileRest }}"
                        @click="mobileOpen = false"
                    >Log in</a>
                    <a
                        href="{{ route('register') }}"
                        class="{{ $mobileLinkBase }} text-white bg-brand-navy hover:bg-brand-navy-800 mt-1 text-center"
                        @click="mobileOpen = false"
                    >Register</a>
                @endauth
            </div>

        </div>
    </div>
    {{-- /Mobile nav panel --}}

</header>
