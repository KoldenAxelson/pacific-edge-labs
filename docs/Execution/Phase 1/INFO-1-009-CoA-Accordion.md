# [INFO-1-009] CoA Accordion - Completion Report

## Metadata
- **Task:** TASK-1-009-CoA-Accordion
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-13
- **Duration:** ~1.5h
- **Status:** ⚠️ Complete with Notes

## What We Did

- Created `resources/views/components/coa/card.blade.php`: Alpine-driven accordion item. Props: `batchNumber`, `testDate`, `purity`, `lab`, `downloadHref`, `isCurrent`, `isOpen`. Collapsed header shows batch number in `font-mono-data`, purity badge (`<x-product.badge variant="purity">`), and optional "Current Batch" badge. Chevron rotates 180° on open via `::class="{ 'rotate-180': open }"` + `transition-smooth`. Expanded state uses `x-collapse x-collapse.duration.400ms` for height, `animate-reveal-left` on inner content div (`:class="open ? 'animate-reveal-left' : ''"`) for the two-phase choreography. Expanded metadata grid is 2-col mobile / 4-col md+. Purity hero value uses `font-mono` + `text-2xl` (not `font-mono-data` — avoids the `font-size: 0.875rem` override conflict). Download button uses `variant="primary"`.
- Created `resources/views/components/coa/accordion-list.blade.php`: Container with shield-check icon, `h3` title (default "Certificates of Analysis"), trust statement, and `space-y-2` slot for `<x-coa.card>` items.
- Created `resources/views/components/coa/summary-strip.blade.php`: Non-interactive compact strip for order confirmations. Cyan-subtle background, shield-check icon, all four metadata values inline with `font-mono-data`. Wraps gracefully on mobile via `flex-wrap`.
- Updated `resources/views/design.blade.php`: Added CoA Accordion section with a three-batch accordion list demo (current batch starts expanded, two prior batches collapsed) and a summary strip demo below. Footer caption updated to TASK-1-001 through TASK-1-009.

## Deviations from Plan

- **`font-mono` used for hero purity value, not `font-mono-data`:** Task spec called for `font-mono-data` in the expanded metadata. However, the hero purity value needs `text-2xl` sizing — and `font-mono-data` sets `font-size: 0.875rem` directly in CSS, which overrides any Tailwind `text-*` utility on the same element. Used `font-mono` (JetBrains Mono via `font-family` only) + `text-2xl` instead. Regular metadata values (`testDate`, `lab`, `batchNumber`) use `font-mono-data` correctly.

- **"Current Batch" badge uses `variant="new"` (cyan):** No dedicated `current` variant exists in `badge.blade.php`. The `new` variant (solid cyan, white text) is the closest semantic match — it signals recency. A dedicated `current` variant can be added later if the distinction matters.

- **Download button uses `variant="primary"` not `variant="outline"`:** Task spec said outline. Switched to primary at the client's direction during review. Noted here for future reference — if the button ever needs to step back visually, revert to `variant="outline"`.

- **`animate-reveal-left` applied to the inner wrapper div, not the `x-collapse` div:** Correct per the established pattern from the design page smoke test. The `x-collapse` div handles height; the inner div carries the animation class. They are siblings in effect — the animation fires when `open` becomes true.

## Confirmed Working

- ✅ Accordion expands and collapses with smooth 400ms height animation via `x-collapse`
- ✅ Content fades in from left with 150ms delay via `animate-reveal-left` on open
- ✅ Chevron rotates 180° on open, returns on close, smooth via `transition-smooth`
- ✅ Purity value visible in collapsed header without expanding
- ✅ Hero purity value renders at `text-2xl` in expanded state — not overridden by `font-mono-data`
- ✅ Metadata grid: 2 cols on mobile, 4 cols md+
- ✅ "Current Batch" badge appears only when `isCurrent` is true
- ✅ `isOpen` prop starts the card expanded on page load
- ✅ Multiple cards stacked in accordion-list with `space-y-2` spacing
- ✅ Summary strip renders all metadata inline, wraps on narrow viewports
- ✅ Design page demo: three batches, current batch open, two collapsed
- ✅ Footer updated to TASK-1-001 through TASK-1-009

## Known Issues

- **⚠️ LOW PRIORITY — Download button visual glitch during open animation:** The button exhibits a reflow/resize appearance during the `x-collapse` expand animation. Root cause: `x-collapse` animates height using `overflow: hidden`, which progressively reveals children top-to-bottom as the container height grows. The button, sitting at the bottom of the container, is the last element to emerge from the clip region — this reads visually as the button "growing into" its final size rather than appearing cleanly. Multiple fixes were attempted: `transform-gpu` on the animating div, separating the button into a sibling div with `animate-fade-in`, pure inline opacity transition with 400ms delay, and moving the button entirely outside the `x-collapse` container. None fully resolved the issue. The most likely clean solution is a JS-driven approach — listen for the collapse transition end event before showing the button, or use a custom `x-transition` on a separately positioned element. Defer to Phase 2 or 3 when the CoA card appears on actual product pages, where the full layout context may offer better options.

## Blockers Encountered

None that blocked delivery. The button animation issue (above) was investigated extensively but deferred rather than blocking task completion.

## Configuration Changes

```
No configuration files modified.
```

## Next Steps

- TASK-1-010 — next Phase 1 task
- Phase 2: CoA cards will appear on product detail pages — revisit the download button animation issue in that context. The full-page layout may allow positioning the button outside the collapse container more naturally.
- Phase 3: `downloadHref` becomes a signed S3 URL. No component changes needed — it's just a prop.

## Files Created/Modified

- `resources/views/components/coa/card.blade.php` — created — single CoA accordion item
- `resources/views/components/coa/accordion-list.blade.php` — created — container with title, trust statement, slot
- `resources/views/components/coa/summary-strip.blade.php` — created — compact non-interactive metadata strip
- `resources/views/design.blade.php` — modified — CoA Accordion demo section added, footer updated to TASK-1-009

---
**For Next Claude:** CoA components live in `resources/views/components/coa/` and are called as `<x-coa.card>`, `<x-coa.accordion-list>`, `<x-coa.summary-strip>`. Key conventions: (1) `isOpen` starts a card expanded on load; (2) `isCurrent` adds a cyan "Current Batch" badge — uses `variant="new"` from `badge.blade.php`; (3) hero purity value in expanded state uses `font-mono text-2xl` NOT `font-mono-data` — the latter sets `font-size: 0.875rem` directly and overrides text size utilities; (4) `downloadHref` is just an `href` prop — S3 signed URLs come in Phase 3; (5) there is a known low-priority visual glitch on the download button during the x-collapse open animation — see Known Issues above.
