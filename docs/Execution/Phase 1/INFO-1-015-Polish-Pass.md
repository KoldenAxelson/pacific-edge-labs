# [INFO-1-015] Polish Pass — Completion Report

## Metadata
- **Task:** TASK-1-015-Polish-Pass
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-16
- **Duration:** ~2h
- **Status:** ✅ Complete

## What We Did

- Added `animate-reveal-right` keyframe and class to `app.css` — 300ms slide from `translateX(16px)`, no delay. Correct entrance direction for bottom-right anchored toast notifications.

- Added `animate-reveal-nav` class to `app.css` — reuses `revealFromBottom` keyframe at 250ms with no delay. Faster variant reserved for navigation panels where `animate-reveal-bottom`'s 100ms `both` fill delay reads as lag.

- Added both new classes to the `@media (prefers-reduced-motion: reduce)` block in `app.css` so all animations are disabled when the OS preference is set.

- Added `active:duration-100` to the primary, secondary, and danger button variants in `button.blade.php`. `transition-smooth` was already applying a 200ms transition globally, which made the `active:scale-[0.98]` press-down feel sluggish. The `active:duration-100` Tailwind utility overrides just the `transition-duration` sub-property during the active state, leaving hover colour transitions at 200ms while the press snaps in ≤100ms.

- Fixed `x-transition:enter` never firing on dynamically added toasts in `toast-container.blade.php`. Root cause: `add()` was pushing toasts with `show: true`, so Alpine created the DOM element with `x-show` already evaluating to true — no reactive change for `x-transition:enter` to intercept. Fix: push with `show: false`, then flip to `show: true` inside `$nextTick` after Alpine finishes the `x-for` render cycle. Also swapped the enter animation from `animate-reveal-bottom` to `animate-reveal-right`.

- Changed `toast.blade.php` `x-transition:enter` from `animate-reveal-bottom` to `animate-reveal-right`. The static toast renders on the design page also needed the correct direction.

- Added entrance animation to the age gate modal in `age-gate.blade.php`. Without this the modal was a hard visual cut. Two additions: `x-transition:enter="animate-fade-in"` on the backdrop div (fades the dark overlay), and `:class="!verified ? 'animate-scale-in' : ''"` on the modal card div (200ms scale pop). The `:class` binding fires on any `verified → false` transition, including the design page's `window._showAgeGate()` trigger.

- Added `tabular-nums` directly to the purity hero `<dd>` in `coa/card.blade.php`. That element uses `font-mono` (the Tailwind utility, which sets the font family only) rather than `font-mono-data` (our custom class, which includes `tabular-nums` but also forces `font-size: 0.875rem`). The hero purity is displayed at `text-2xl`, so `font-mono-data` would have overridden the size. The class needed to be applied separately.

- Added `ring-2 ring-white` to the cart badge `<span>` in `navigation.blade.php`. Without a ring the cyan badge bleeds into the cart icon strokes at the overlap point, reading as part of the icon rather than a separate counter element.

- Created `POLISH-NOTES.md` in the project root documenting every change with rationale (required deliverable per task spec).

## Deviations from Plan

- **`accordion-list.blade.php` — no changes needed:** The task spec listed the CoA accordion chevron transition as something to verify. On inspection the chevron already had `::class="{ 'rotate-180': open }"` and `transition-smooth` correctly applied. Nothing changed.

- **`badge.blade.php` — no changes needed:** The task spec listed `tabular-nums` on purity percentage values as an open item. The purity badge already renders its value inside `<span class="font-mono-data">`, and `font-mono-data` sets `font-variant-numeric: tabular-nums` in `app.css`. Already satisfied.

- **Sidebar panel transition kept at `transition-medium` (300ms):** The task spec suggested considering 250ms for nav interactions. That concern was specifically about `animate-reveal-bottom` (the old class for the sidebar). The sidebar uses a `translate-x-full → translate-x-0` slide, which reads correctly at 300ms. The `animate-reveal-nav` class (250ms, no delay) was added to `app.css` for future use if a bottom-reveal nav pattern is introduced, but was not applied to the existing sidebar.

- **`toast-container.blade.php` required more than an animation class swap:** The original diagnosis assumed only the class name was wrong. The deeper issue was that `x-transition:enter` fundamentally never fired due to Alpine's `x-for` + `x-show` render timing. This required the `show: false` → `$nextTick` fix in addition to the class change.

- **`design.blade.php` footer caption not updated:** The caption still reads "TASK-1-001 through TASK-1-011" — a carried-forward artifact first noted in INFO-1-014. Unchanged here as it was not part of the Polish Pass scope.

## Confirmed Working

- ✅ Toast enter animation fires on each `_showToast()` call — slides in from the right at 300ms
- ✅ Toast leave animation still works — fades out over 300ms (`transition-medium`)
- ✅ Multiple toasts stack and each animates in independently
- ✅ Age gate backdrop fades in (`animate-fade-in`) and card scales in (`animate-scale-in`) on both fresh page load and `window._showAgeGate()` trigger
- ✅ Button press responds in ≤100ms — `active:duration-100` overrides the 200ms `transition-smooth` base during active state only
- ✅ Purity hero in CoA card expanded body displays with `tabular-nums` — digit widths consistent across `99.4%`, `98.9%`, `98.4%`
- ✅ Cart badge has visible white ring separating it from the cart icon
- ✅ `@media (prefers-reduced-motion: reduce)` block covers all 7 animation classes: `animate-reveal-left`, `animate-reveal-bottom`, `animate-reveal-right`, `animate-reveal-nav`, `animate-scale-in`, `animate-fade-in`, `animate-stagger`

## Important Notes

- **`x-transition:enter` does not fire on `x-for` items created with `x-show` already true.** Alpine has nothing to intercept if the element enters the DOM already visible. The fix pattern is: push the item with `show: false`, then flip to `show: true` inside `$nextTick`. This is a universal Alpine gotcha — any future dynamic list that uses `x-for` + `x-show` + `x-transition:enter` must follow this pattern.

- **`font-mono-data` sets font-size: 0.875rem.** It cannot be used on elements with a custom text size override (`text-2xl`, `text-h3`, etc.) — the custom class will win specificity depending on order, but the intent is lost either way. Apply `font-mono` (family only) + `tabular-nums` (Tailwind utility) separately on large mono values.

- **`animate-reveal-nav` is in `app.css` but not yet applied anywhere.** It was added in anticipation of future bottom-reveal nav patterns (mobile drawers in Phase 2, etc.). The existing sidebar uses translate-x and does not need it.

- **The `active:duration-100` pattern works because Tailwind v4 generates `active:` variants for `duration-*` utilities.** This is not universally supported in older Tailwind versions. Worth noting if the project ever needs to downgrade.

## Blockers Encountered

- **`x-transition:enter` silently not firing on toasts** → The animation class swap alone had no effect because the transition never had a chance to run. Root cause identified as Alpine not observing a state change on newly created `x-for` elements with `show: true`. Resolved by pushing with `show: false` and flipping in `$nextTick`.

## Configuration Changes

None.

## Next Steps

- Phase 1 is complete. All 15 tasks done.
- Next: TASK-2-000 — Phase 2 overview and planning.
- The `design.blade.php` footer caption ("TASK-1-001 through TASK-1-011") should be updated to "TASK-1-001 through TASK-1-015" when next touching that file.

## Files Created/Modified

- `resources/css/app.css` — modified — added `animate-reveal-right`, `animate-reveal-nav`, both in reduced-motion block
- `resources/views/components/ui/button.blade.php` — modified — `active:duration-100` on primary, secondary, danger variants
- `resources/views/components/ui/toast.blade.php` — modified — `x-transition:enter` swapped to `animate-reveal-right`
- `resources/views/components/ui/toast-container.blade.php` — modified — `show: false` + `$nextTick` fix for enter animation; `x-transition:enter` swapped to `animate-reveal-right`
- `resources/views/components/compliance/age-gate.blade.php` — modified — `animate-fade-in` on backdrop, `animate-scale-in` on modal card
- `resources/views/components/coa/card.blade.php` — modified — `tabular-nums` on purity hero `<dd>`
- `resources/views/components/ui/navigation.blade.php` — modified — `ring-2 ring-white` on cart badge `<span>`
- `POLISH-NOTES.md` — created — running log of every polish change with rationale

---
**For Next Claude:** The most important gotcha from this task: `x-transition:enter` will silently never fire on `x-for` list items if those items are pushed into the array with `show: true`. Always push with `show: false` and flip in `$nextTick`. This pattern must be followed any time a dynamic list uses `x-for` + `x-show` + enter transitions — it is not obvious from the Alpine docs and produces no error, just a missing animation. See `toast-container.blade.php` for the reference implementation.
