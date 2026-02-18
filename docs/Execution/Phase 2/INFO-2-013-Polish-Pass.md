# [INFO-2-013] Phase 2 Polish Pass - Completion Report

## Metadata
- **Task:** TASK-2-013-Polish-Pass
- **Phase:** 2 (Product Catalog & Pages)
- **Started:** 2026-02-17
- **Last Updated:** 2026-02-18
- **Duration:** Multi-session (code review + extensive UI/UX polish + dark mode audit)
- **Status:** ✅ Complete

## What We Did

### Pass 1: Code Review (2026-02-17)
Performed review of all Phase 2 sign-off criteria and updated the Phase 2 index. Fixed N+1 query in CategoryController.

### Pass 2: Mobile & UX Polish (2026-02-17)
Five polish items implemented based on iPhone testing:

1. **Mobile scroll-to-top button** — Fixed button at bottom-right of All Products page. Alpine-driven, appears after 400px scroll, smooth-scrolls to top. `md:hidden` so desktop-only users never see it.

2. **iOS Safari backdrop safe area** — Extended hamburger overlay backdrop to `top: -50px; bottom: -50px` to cover the iOS safe area behind the URL bar. Added `viewport-fit=cover` to the viewport meta tag.

3. **iOS Safari bottom overscroll** — Set `html { background: var(--color-navy) }` and `overscroll-behavior-y: none` to prevent white flash on iOS bounce-scroll at page bottom.

4. **Navigation links** — Wired up hamburger menu with: Home, All Products, dynamic category sub-links (indented), divider, About/FAQ/Contact placeholders. Footer Products column now dynamically pulls categories from database.

5. **Dark mode toggle** — Cookie-persisted (`pel_dark`) dark mode via CSS custom property overrides. Anti-FOUC inline script in `<head>` applies `html.dark` before first paint. Toggle switch in hamburger sidebar with sun/moon icons and animated pill.

### Pass 3: Bug Fixes (2026-02-17)
- **Filament `$navigationGroup` type mismatch** — Changed `?string` to `string|UnitEnum|null` in ProductResource and CategoryResource to match Filament 4 parent signature.
- **`Section`/`Grid` import error** — Moved imports from `Filament\Forms\Components\*` to `Filament\Schemas\Components\*` in ProductForm and CategoryForm (Filament 4 namespace change).
- **`schema-product.blade.php` parse error** — Rewrote JSON-LD generation from inline Blade directives to PHP `json_encode()` to avoid Blade parser confusion with `@if`/`@foreach` inside `<script>` tags within `@push` blocks.

### Pass 4: Dark Mode Audit & Overhaul (2026-02-18)
Comprehensive audit identified a fundamental flaw in the CSS variable swap approach: `--color-navy` serves dual duty as text color (should invert) AND background color for intentionally-dark elements (should NOT invert). Implemented a systematic fix:

**Architecture: `.dark-stable` marker class system**
- One CSS class for any element whose `bg-brand-navy` should stay visually dark in dark mode
- `.btn-secondary` / `.btn-outline` variant markers for button-specific treatment
- `.toast-dark` marker for toast notifications
- All overrides organized in a documented 5-tier CSS section

**Elements fixed:**
- **Product card** — Added `product-card` class + `border-top` divider between image and content zones in dark mode to restore two-tone visual distinction
- **Announcement banner** — Marked `.dark-stable` to keep dark navy + white text in both modes
- **Secondary buttons** — `btn-secondary` marker: dark slate bg (#1e293b) + light text in dark mode
- **Outline buttons** — `btn-outline` marker: gray border/text on dark bg, solid fill on hover
- **Toast (static)** — `toast-dark` marker on `x-ui.toast` component
- **Toast (dynamic)** — `toast-dark` marker on `toast-container.blade.php` JS-driven toasts
- **Category badge** — Swapped hardcoded `bg-white border-brand-navy text-brand-navy` to token-based `bg-brand-surface border-brand-border-dark text-brand-text`
- **Logo beaker icons** — `.dark-stable` on header + sidebar wordmark icons
- **User avatar circles** — `.dark-stable` on header + sidebar avatar backgrounds
- **Register button** — `.dark-stable` with hover going darker (#0a1121) instead of lighter
- **Age Gate header** — `.dark-stable` on modal's dark navy top section
- **Alert component** — Dark mode overrides: `text-*-900` → `text-*-300`, `border-*-200` → `border-*-600`
- **Scroll-to-top button** — `.dark-stable` to keep dark appearance

**CSS reorganization:**
Replaced scattered overrides with documented 5-tier system in `app.css`:
1. Dark-stable elements (`.dark-stable` marker class)
2. Button variant overrides (`.btn-secondary`, `.btn-outline`)
3. Structural overrides (html, header, footer, sidebar, product card)
4. Utility overrides (bg-white, ring-white)
5. Component overrides (toast-dark, alert text/border colors)

### Pass 5: Final Polish (2026-02-18)
- **Dark mode toggle travel** — Enlarged pill from `h-5 w-9` to `h-6 w-11`, dot from `h-3.5 w-3.5` to `h-4 w-4`, slide travel from 12px to 23px
- **Search bar flash on page load** — Added `x-cloak` to search container to prevent border artifact before Alpine initializes `:class` binding
- **Nav icons sliding on page load** — Added `flex-1` to static class on spacer divs so layout is correct before Alpine boots (prevents the [search, cart, hamburger] group from animating from center to right on every page navigation)

## Known Issues
- **iOS Safari safe area (home indicator zone)** — The white strip beneath the footer/overlay on iPhone is an iOS Safari rendering behaviour where the browser composites its own layer behind the home indicator. Standard CSS fixes (`padding-bottom: env(safe-area-inset-bottom)`, `viewport-fit=cover`, Alpine guard element) were attempted but had no visible effect. The `padding-bottom` rules on footer and `#mobile-nav` are kept as they are correct CSS practice. Tracked for future investigation — may require native PWA approach or iOS update.
- **og-default.png missing** — SEO partial references `asset('images/og-default.png')` but the file doesn't exist yet
- **Orphaned Livewire files** — From INFO-2-009, three files still need manual deletion

## Files Created/Modified
### CSS
- `resources/css/app.css` — Dark mode overrides (5-tier system), iOS safe area padding, overscroll fixes, dark mode variable overrides for html.dark

### Blade Components
- `resources/views/components/ui/button.blade.php` — Added `btn-secondary`, `btn-outline` marker classes
- `resources/views/components/ui/toast.blade.php` — Added `toast-dark` marker class
- `resources/views/components/ui/toast-container.blade.php` — Added `toast-dark` marker class
- `resources/views/components/product/card.blade.php` — Added `product-card` marker class
- `resources/views/components/product/badge.blade.php` — Category variant: swapped to token-based colors
- `resources/views/components/compliance/age-gate.blade.php` — Added `dark-stable` to header section

### Layouts
- `resources/views/layouts/app.blade.php` — viewport-fit=cover, title/meta slots, dark mode anti-FOUC script, @stack('schema'), known issue comment
- `resources/views/layouts/navigation.blade.php` — Dark-stable markers (announcement bar, logos, avatars, register button), x-cloak on search bar, flex-1 on spacers, dark mode toggle with enlarged pill, wired nav links

### Pages
- `resources/views/products/index.blade.php` — Scroll-to-top button (right-aligned, dark-stable), SEO slots
- `resources/views/products/show.blade.php` — SEO integration (title, meta, schema)
- `resources/views/categories/show.blade.php` — SEO integration
- `resources/views/partials/schema-product.blade.php` — Rewritten with PHP json_encode()
- `resources/views/partials/seo-meta.blade.php` — Created (meta description, canonical, OG, Twitter Card)

### Controllers
- `app/Http/Controllers/CategoryController.php` — Added 'category' to eager load

### Docs
- `docs/Execution/Phase 2/PHASE-2-INDEX.md` — Sign-off checklist updated

---
**For Next Claude:** Dark mode is implemented via CSS custom property swap (`html.dark` overrides `:root` tokens). The key pattern for elements that should stay dark in dark mode is the `.dark-stable` marker class — add it alongside `bg-brand-navy` on any intentionally-dark element. Button variants use `.btn-secondary` / `.btn-outline` markers. The iOS Safari safe area issue is unresolved — standard CSS approaches don't affect the browser's composited home indicator layer. All Alpine-driven UI (search, mobile nav, dark toggle, toasts) requires static classes matching the default `:class` state to prevent FOUC/layout jumps before Alpine initializes.
