# [TASK-1-012] Navigation Header & Footer

## Objective
Build the sticky site header, mobile nav panel, nav link component, and the footer. These frame every page. The header needs to land the brand identity immediately — the wordmark treatment is deliberate and specific.

## Design Direction
Header: white background, 1px bottom border, sticky. Clean and structured — no colored header backgrounds. The nav link active state is the one place the cyan accent appears in navigation.

Footer: deep navy background. Provides visual closure and contrast against the off-white page. The compliance disclaimer strip runs inside the footer above the link columns — it's always present, not just on product pages.

Wordmark treatment: "Pacific Edge" in DM Sans semibold, "Labs" in JetBrains Mono at 10px with wide tracking (0.2em) and uppercase beneath it. The mono font on "Labs" is a deliberate micro-detail — it signals precision and specialization. Beaker icon in a small navy rounded-lg box with cyan icon sits left of the wordmark.

## Deliverables

### Replace `resources/views/layouts/navigation.blade.php`
Breeze's default nav is replaced entirely.

**Header structure:**
- Sticky `z-40`, white, bottom border
- Container `size="lg"` — full width up to 1280px
- Left: logo/wordmark (link to `/`)
- Center: desktop nav links (hidden on mobile), `<x-ui.nav-link>` for each route
- Right: search icon button, cart icon with `0` badge, auth buttons (login/register when guest, avatar chip when authed)
- Mobile: hamburger icon button, triggers `mobileOpen = true`

**Mobile nav panel:**
- `x-show="mobileOpen"`, `x-cloak`
- Positioned `absolute top-full inset-x-0` — drops below header
- `x-transition` enter/leave: `animate-reveal-bottom` on enter, quick fade on leave
- `@click.outside="mobileOpen = false"` and `@keydown.escape.window="mobileOpen = false"`
- Full-width nav links (block, rounded-xl, hover bg)
- Auth section below a divider

**Cart badge:** hardcoded `0` for Phase 1. Cyan background, white text, positioned `-top-0.5 -right-0.5`. Phase 4 makes it dynamic.

### `resources/views/components/ui/nav-link.blade.php`
Props: `href`, `active` (bool, auto-detected via `request()->is()` if not explicitly set).

Active state: `text-brand-cyan bg-brand-cyan-subtle` pill. Rest state: `text-brand-navy-700 hover:bg-brand-surface-2 hover:text-brand-navy`. Adds `aria-current="page"` when active.

### `resources/views/components/ui/footer.blade.php`
Structure:
1. `<x-compliance.disclaimer-banner variant="footer">` at top
2. 4-column grid: Brand column (wordmark + tagline "U.S.A. Tested · Potency Verified · Purity Quantified" + three credential lines), Products column, Company column, Legal column
3. Bottom bar: copyright + "All products for research use only" note

Navy background throughout. Link colors: `text-slate-400 hover:text-brand-cyan`. Column headers: `text-white uppercase tracking-wider text-body-sm font-semibold`.

Legal links must include: Terms of Service, Privacy Policy, Refund Policy, Shipping Policy, Research Use Policy. These pages don't exist yet — `href` placeholders are fine.

### Update `resources/views/layouts/app.blade.php`
Wire in: `@include('layouts.navigation')`, `<x-compliance.disclaimer-banner variant="page-top" />`, `<main>`, `<x-ui.footer />`.

## Acceptance Criteria
- [ ] Header sticks to top on scroll, sits at `z-40` (below age gate at `z-50`)
- [ ] Wordmark: "Pacific Edge" DM Sans + "Labs" mono treatment renders correctly
- [ ] Desktop nav links show active state on current route
- [ ] Cart icon shows cyan badge
- [ ] Mobile hamburger shows on `< md`, desktop nav hides on `< md`
- [ ] Mobile panel opens with `animate-reveal-bottom`, closes on outside click and Escape
- [ ] Footer disclaimer banner renders in amber above the nav columns
- [ ] Footer copyright line includes current year via `{{ date('Y') }}`
- [ ] All legal link placeholders present in footer

## Notes
The "U.S.A. Tested · Potency Verified · Purity Quantified" tagline from the current Wix site is genuinely strong copy. It earns its place in the footer brand column. Keep it verbatim.

The sticky header at `z-40` intentionally sits below the age gate (`z-50`) and below any future modals. Establish this z-index convention now:
- `z-30` — sticky secondary navs (design page anchor nav, etc.)
- `z-40` — main site header
- `z-50` — modals, overlays, age gate
- `z-60` — toast notifications (must appear above everything)

---
**Sequence:** 12 of 15 — depends on TASK-1-003 through TASK-1-010
**Estimated time:** 4–5 hours
