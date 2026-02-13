# [TASK-1-005] Button Components

## Objective
Build `<x-ui.button>`, `<x-ui.button-group>`, and `<x-ui.icon-button>`. Pill-shaped, brand-palette, covers every interaction on the site.

## Design Direction
Fully rounded (`rounded-full`). Primary is cyan — used for the highest-value action on any given screen. Secondary is navy. The `active:scale-[0.98]` micro-interaction is required on all filled variants — it makes buttons feel physically pressed.

## Deliverables

### `resources/views/components/ui/button.blade.php`
Props: `variant` (primary/secondary/outline/danger/ghost), `size` (sm/md/lg), `type`, `href`, `disabled`, `icon` (heroicon name, left), `iconEnd` (heroicon name, right).

If `href` is set, renders as `<a>`. Otherwise `<button>`.

Variant styles:
- **primary** — `bg-brand-cyan text-white hover:bg-brand-cyan-dark`
- **secondary** — `bg-brand-navy text-white hover:bg-brand-navy-800`
- **outline** — `border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white`
- **danger** — `bg-red-600 text-white hover:bg-red-700`
- **ghost** — `text-brand-navy hover:bg-brand-surface-2`

All filled variants get `active:scale-[0.98]` and `transition-smooth`.
Focus ring: `focus:outline-none focus:ring-2 focus:ring-offset-2` with variant-appropriate ring color.
Disabled: `opacity-50 cursor-not-allowed pointer-events-none`.

### `resources/views/components/ui/button-group.blade.php`
`flex flex-wrap items-center` with configurable gap. Props: `gap` (default 3).

### `resources/views/components/ui/icon-button.blade.php`
Square icon-only button. Props: `icon`, `variant` (ghost/primary/danger), `size` (sm/md/lg), `label` (required — aria-label), `href`. Always `rounded-full`. Minimum touch target 44px.

## Acceptance Criteria
- [ ] All 5 variants render with correct colors and hover states
- [ ] All 3 sizes have correct padding/height
- [ ] `href` prop correctly renders `<a>` tag
- [ ] `icon` prop renders heroicon left-aligned
- [ ] `active:scale-[0.98]` press feedback works on primary and secondary
- [ ] Focus ring visible on keyboard navigation
- [ ] `disabled` prevents all interaction
- [ ] Icon button meets 44px minimum tap target

## Notes
Heroicons package — confirm `blade-ui-kit/blade-heroicons` is in `composer.json`. If not, `sail composer require blade-ui-kit/blade-heroicons`.

Pill buttons at `size="sm"` can look overly round in some contexts. Check the product card Add to Cart button at small size — if it looks wrong, consider `rounded-xl` as an alternative for that specific use case (addressed in TASK-1-008).

---
**Sequence:** 5 of 15 — depends on TASK-1-001 through TASK-1-004
**Estimated time:** 2–3 hours
