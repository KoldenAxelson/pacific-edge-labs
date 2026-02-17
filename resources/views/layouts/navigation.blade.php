{{--
  [TASK-1-012 rev.4] Site Header — with inline animated search
  Architecture notes:

  Alpine scope: x-data lives on <body> in app.blade.php.
  Body-level state includes: mobileOpen, searchOpen, searchQuery, searchResults,
  searchCount, searchLoading, searchSearched, openSearch(), closeSearch(), doSearch().

  Search animation flow:
    1. User clicks search icon → openSearch()
    2. Center nav (product anchors) + right buttons (cart, auth, hamburger) fade out via CSS transition
    3. Search input stretches from w-0 to flex-1, filling available header space
    4. Cancel (X) button scales in at far right
    5. Input autofocuses — user types immediately
    6. On results: tray x-collapses open below header bar with compact horizontal-scroll cards
    7. Cancel reverses everything — bar shrinks, buttons fade back, tray collapses
    8. Mobile product sub-nav also fades out when searching (handled in products/show.blade.php)

  Z-index convention:
    z-30  secondary sticky subnav
    z-40  main site header + backdrop
    z-50  sidebar, modals, age gate
    z-60  toast notifications
--}}

{{-- ── Announcement bar ──────────────────────────────────────────────────── --}}
<div class="w-full bg-brand-navy">
    <p class="text-center text-caption py-2 px-4">
        <span class="text-white">Free U.S. shipping on orders $150+</span>
        <span class="mx-2 text-brand-cyan" aria-hidden="true">&bull;</span>
        <span class="text-white">Free international shipping on orders $500+</span>
    </p>
</div>

{{-- ── Sticky header ─────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-40 bg-white border-b border-brand-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16 gap-3">

            {{-- Left: Wordmark (always visible) --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0" aria-label="Pacific Edge Labs — home">
                <div class="w-8 h-8 bg-brand-navy rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-beaker class="w-4 h-4 text-brand-cyan" aria-hidden="true" />
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-heading font-semibold text-brand-navy text-base leading-tight">Pacific Edge</span>
                    <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-brand-navy-500 leading-tight mt-0.5">Labs</span>
                </div>
            </a>

            {{-- Left spacer (keeps center nav centered; collapses when searching) --}}
            <div
                :class="searchOpen ? 'w-0 flex-none' : 'flex-1'"
                class="transition-all duration-300 ease-out"
            ></div>

            {{-- Center: Product anchor nav (product pages only — fades out when searching) --}}
            @if(request()->routeIs('products.show'))
                <nav
                    :class="searchOpen ? 'opacity-0 pointer-events-none scale-95' : 'opacity-100 scale-100'"
                    class="hidden md:flex items-center gap-1 flex-shrink-0 justify-center transition-all duration-200 ease-in"
                    aria-label="Page sections"
                >
                    @foreach(['overview' => 'Overview', 'specifications' => 'Specs', 'description' => 'Description', 'research' => 'Research', 'coa' => 'CoA'] as $anchor => $label)
                        <a
                            href="#{{ $anchor }}"
                            class="px-2.5 py-1 text-body-sm font-medium text-brand-navy-600 hover:text-brand-cyan hover:bg-brand-surface-2 rounded-lg transition-smooth"
                        >{{ $label }}</a>
                    @endforeach
                </nav>
            @endif

            {{-- Right spacer (mirrors left spacer; collapses when searching) --}}
            <div
                :class="searchOpen ? 'w-0 flex-none' : 'flex-1'"
                class="transition-all duration-300 ease-out"
            ></div>

            {{-- Search bar + cancel grouped together (stretches from nothing to fill available space) --}}
            <div
                :class="searchOpen ? 'flex-1 opacity-100' : 'w-0 opacity-0 overflow-hidden'"
                class="flex items-center gap-2 transition-all duration-300 ease-out min-w-0"
            >
                <div class="relative flex-1 min-w-0">
                    <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-brand-text-muted">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input
                        id="nav-search-input"
                        type="search"
                        x-model="searchQuery"
                        @input="doSearch()"
                        @keydown.escape="closeSearch()"
                        placeholder="Search compounds, SKUs..."
                        class="w-full pl-9 pr-8 py-2 rounded-lg border border-brand-border bg-brand-surface text-body-sm text-brand-text placeholder:text-brand-text-faint focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan/20 focus:outline-none transition-smooth"
                    />
                    {{-- Inline loading spinner --}}
                    <div
                        x-show="searchLoading"
                        x-transition.opacity.duration.150ms
                        class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2"
                    >
                        <svg class="animate-spin w-4 h-4 text-brand-cyan" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Cancel search button (right next to the input) --}}
                <button
                    type="button"
                    @click="closeSearch()"
                    class="flex-shrink-0 p-2 text-brand-navy-500 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Close search"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Right: Action buttons (fade out when searching) --}}
            <div
                :class="searchOpen ? 'opacity-0 pointer-events-none w-0 overflow-hidden' : 'opacity-100'"
                class="flex items-center gap-0.5 flex-shrink-0 transition-all duration-200 ease-in"
            >

                {{-- Search trigger --}}
                <button
                    type="button"
                    @click="openSearch()"
                    class="p-2 text-brand-navy-600 hover:text-brand-navy hover:bg-brand-surface-2 rounded-lg transition-smooth"
                    aria-label="Search products"
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
                        class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-cyan text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none pointer-events-none ring-2 ring-white"
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
                @endauth

                {{-- Morphing hamburger --}}
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
            {{-- /Right buttons --}}

        </div>
    </div>

    {{-- ── Search results tray (x-collapses below header bar) ─────────── --}}
    <div
        x-show="searchOpen && searchTrayOpen"
        x-collapse
        x-cloak
        class="border-t border-brand-border bg-brand-surface"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- No results --}}
            <div x-show="searchCount === 0" class="py-4 text-center text-sm text-brand-text-muted">
                No compounds matched "<span x-text="searchQuery" class="font-medium text-brand-navy"></span>"
            </div>

            {{-- Result cards — horizontal scroll with staggered fade-in-from-bottom --}}
            <div x-show="searchCount > 0" class="py-3">
                <style>
                    @keyframes fadeUpIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to   { opacity: 1; transform: translateY(0); }
                    }
                    .search-card-enter {
                        animation: fadeUpIn 0.3s ease-out both;
                    }
                </style>
                <div class="flex items-start gap-3 overflow-x-auto pb-1 -mx-1 px-1 snap-x">
                    <template x-for="(item, idx) in searchResults" :key="item.url">
                        <a
                            :href="item.url"
                            :style="'animation-delay: ' + (idx * 60) + 'ms'"
                            class="search-card-enter flex-shrink-0 w-36 sm:w-40 group rounded-lg border border-brand-border bg-white hover:border-brand-cyan transition-smooth overflow-hidden snap-start"
                        >
                            {{-- Tiny image --}}
                            <div class="aspect-square bg-brand-surface-2 overflow-hidden">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-heroicon-o-beaker class="w-8 h-8 text-brand-text-faint" />
                                    </div>
                                </template>
                            </div>
                            {{-- Info --}}
                            <div class="p-2 space-y-0.5">
                                <p class="text-[10px] font-medium tracking-widest uppercase text-brand-text-muted truncate" x-text="item.category"></p>
                                <h4 class="text-caption font-semibold text-brand-navy leading-snug line-clamp-1" x-text="item.name"></h4>
                                <span class="text-caption font-semibold text-brand-cyan" x-text="item.price"></span>
                            </div>
                        </a>
                    </template>
                </div>
                <p class="text-[11px] text-brand-text-muted mt-1">
                    <span x-text="searchCount"></span> <span x-text="searchCount === 1 ? 'result' : 'results'"></span>
                </p>
            </div>

        </div>
    </div>

</header>
{{-- /Sticky header --}}


{{-- ── Backdrop ──────────────────────────────────────────────────────────── --}}
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
    class="fixed bg-black/40 z-40"
    style="top: -50px; left: 0; right: 0; bottom: -50px;"
    aria-hidden="true"
></div>


{{-- ── Sidebar ───────────────────────────────────────────────────────────── --}}
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
        <a
            href="{{ route('products.index') }}"
            class="{{ $link }} {{ request()->routeIs('products.index') ? $active : $rest }}"
            @if(request()->routeIs('products.index')) aria-current="page" @endif
            @click="mobileOpen = false"
        >All Products</a>

        {{-- Category sub-links (indented) --}}
        @php $navCategories = \App\Models\Category::active()->ordered()->get(); @endphp
        @foreach($navCategories as $navCat)
            <a
                href="{{ route('categories.show', $navCat->slug) }}"
                class="{{ $link }} pl-8 text-caption {{ request()->is('categories/' . $navCat->slug) ? $active : $rest }}"
                @click="mobileOpen = false"
            >{{ $navCat->name }}</a>
        @endforeach

        <div class="h-px bg-brand-border my-2 mx-4"></div>

        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">About</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">FAQ</a>
        <a href="#" class="{{ $link }} {{ $rest }}" @click="mobileOpen = false">Contact</a>
    </nav>

    {{-- Dark mode toggle --}}
    <div class="px-3 py-2 border-t border-brand-border flex-shrink-0">
        <button
            type="button"
            @click="toggleDark()"
            class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl text-body-sm font-medium transition-smooth text-brand-navy-700 hover:bg-brand-surface-2 hover:text-brand-navy"
        >
            <span class="flex items-center gap-2.5">
                <template x-if="!darkMode">
                    <x-heroicon-o-moon class="w-4.5 h-4.5" />
                </template>
                <template x-if="darkMode">
                    <x-heroicon-o-sun class="w-4.5 h-4.5" />
                </template>
                <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
            </span>
            {{-- Toggle pill --}}
            <span
                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200"
                :class="darkMode ? 'bg-brand-cyan' : 'bg-brand-text-faint'"
            >
                <span
                    class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform duration-200"
                    :class="darkMode ? 'translate-x-4' : 'translate-x-1'"
                ></span>
            </span>
        </button>
    </div>

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
