# [TASK-1-009] CoA Accordion Component

## Objective
Build the Certificate of Analysis display components. This is PEL's clearest competitive differentiator — competitors require email requests, PEL shows CoA data directly on the product page. The accordion pattern keeps the product page clean while making data available on demand.

## Design Direction
The VisorPlate FAQ choreography applied here: `x-collapse` handles the height, `animate-reveal-left` fires on the content after a 150ms delay. The purity percentage is visible in the collapsed header so customers can scan all batches without expanding. Expanding reveals the full metadata grid and download link.

## Deliverables

### `resources/views/components/coa/card.blade.php`
Single CoA accordion item. Props: `batchNumber`, `testDate`, `purity`, `lab`, `downloadHref`, `isCurrent` (shows "Current Batch" badge), `isOpen` (start expanded, defaults false).

**Collapsed state (always visible):**
- Batch number in `font-mono-data`
- Purity badge (`<x-product.badge variant="purity">`) — scannable without expanding
- "Current Batch" badge if `isCurrent`
- Chevron rotates 180° when open (`::class="{ 'rotate-180': open }"` + `transition-smooth`)

**Expanded state (x-collapse, 400ms):**
- Content div gets `animate-reveal-left` class when `open === true`
- Metadata displayed as a `<dl>` grid: Purity | Test Date | Laboratory | Batch — 2 or 4 cols depending on viewport
- Purity value in `font-mono-data` cyan (larger than in header — this is the hero number)
- Download button: outline variant, `arrow-down-tray` icon, opens in new tab

Border: `border border-brand-border rounded-2xl`. No shadow — stays with the thin-border surface system.

### `resources/views/components/coa/accordion-list.blade.php`
Container with title, shield-check icon, and a one-line trust statement. Props: `title` (default "Certificates of Analysis"). Renders slot of `<x-coa.card>` items with `space-y-2`.

Trust statement below the title: *"Third-party tested for purity and potency. Full documentation available for every batch."* — small, muted, but it signals process rigor.

### `resources/views/components/coa/summary-strip.blade.php`
Non-interactive, compact. Used in order confirmations. Cyan-subtle background, shield-check icon, all batch metadata inline. Props: `batchNumber`, `testDate`, `purity`, `lab`.

## Acceptance Criteria
- [ ] Accordion collapses and expands with smooth height transition (`x-collapse` 400ms)
- [ ] Content fades in from left with 150ms delay after expand starts
- [ ] Chevron rotates smoothly on open/close
- [ ] Purity value visible in collapsed header without expanding
- [ ] Expanded metadata grid is readable — correct responsive column behavior
- [ ] Download button renders (href doesn't need to work — S3 is Phase 3)
- [ ] Multiple cards stacked in accordion-list have consistent spacing
- [ ] Summary strip renders all metadata in one row, wraps gracefully on mobile

## Notes
The `animate-reveal-left` class should only apply when `open` is true. When the accordion closes, the content disappears via `x-collapse` — no exit animation needed (fighting the collapse height animation creates jank).

In Phase 3, `downloadHref` becomes a signed S3 URL. The component doesn't need to care — it's just an `href` prop.

---
**Sequence:** 9 of 15 — depends on TASK-1-004, TASK-1-005, TASK-1-007
**Estimated time:** 2–3 hours
