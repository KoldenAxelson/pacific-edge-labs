# [INFO-1-008] Product Card - Completion Report

## Metadata
- **Task:** TASK-1-008-Product-Card
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-13
- **Duration:** ~45m
- **Status:** ✅ Complete

## What We Did

- Created `resources/views/components/product/card.blade.php`: Alpine-driven product card with hover blur/reveal, conditional corner status badge, slash price support, and composited badge + button primitives. Props: `name`, `category`, `price`, `originalPrice`, `purity`, `batchNumber`, `batchStatus`, `href`, `imageSrc`, `imageAlt`, `researchSummary`. Corner badge driven by a PHP priority cascade (`sale` > `low_stock` > `out_of_stock` > nothing). Image area uses `x-data="{ hovered: false }"` with `:class` binding for blur/dim; research summary uses `x-show` + `x-transition` opacity fade. If no `researchSummary` prop is passed, the overlay `@if` block is absent from the DOM entirely — image still blurs, no text appears. Purity and batch badges always visible in card body, never touched by hover state.
- Created `resources/views/components/product/card-skeleton.blade.php`: `animate-pulse` placeholder mirroring card's exact structure. Includes `aspect-[4/3]` image block, two-line name placeholder, badge-shaped rounded-full elements, corner badge placeholder, and footer with price + button slots. No logic.
- Added `sale` variant to `resources/views/components/product/badge.blade.php`: solid `bg-brand-success` background, white text, tag icon (`heroicon-o-tag`), label defaults to `'Sale'`. Inserted before `in_stock` in the match block.
- Updated `resources/views/design.blade.php`: replaced prior card demos with revised set covering all corner states (in stock — clean, low stock — amber corner, out of stock — gray corner + disabled CTA), plus a dedicated sale demo row (Hospira slash price, Semax sale + low stock verifying priority logic). Badge demo section updated to include `sale` variant next to `new`. Footer caption updated to `TASK-1-001 through TASK-1-008`.

## Deviations from Plan

- **Research Only badge removed from card:** Original spec placed the research badge permanently in the image corner. After review, this was redundant — every product on the site is research-only, so the badge communicates nothing that context doesn't already establish. It will appear on individual product pages where it carries more weight. The corner slot is now reserved for status signals that actually vary card-to-card.

- **In Stock badge removed from card footer:** Presence on the shelf is assumed. Silence communicates in stock. The footer is now price + CTA only — cleaner, and leaves room for slash pricing without crowding.

- **Corner badge slot replaces both:** The image corner now carries `sale`, `low_stock`, or `out_of_stock` only. Priority cascade: `sale` wins over `low_stock` (more actionable, and the discounted price already communicates urgency). Nothing renders for in-stock products.

- **`originalPrice` prop added (not in original spec):** Passing `originalPrice` is the sole trigger for sale treatment — no separate boolean needed. Its presence stacks the footer to: struck-through original price in `font-mono-data` muted, then sale price in `text-h4 text-brand-success`. Absence keeps the single-line price layout unchanged.

- **Sale badge added to `badge.blade.php`:** Not specified in the original task (which only covered the card component), but the corner slot required it. Kept consistent with existing variant conventions.

- **`x-transition` used instead of `animate-fade-in` for hover overlay:** `animate-fade-in` is a one-shot CSS animation — it fires once on element insertion and stops. It cannot drive a repeated enter/leave triggered by hover. `x-transition` with opacity classes is the correct mechanism for toggled Alpine visibility.

- **`font-mono-data` on struck-through price only, not sale price:** `font-mono-data` sets `font-size: 0.875rem` explicitly in CSS, which would override `text-h4` (1.25rem) on the sale price. The struck original is rendered at normal body size and benefits from tabular-nums alignment; the sale price needs headline weight, so it stays on DM Sans via `text-h4`.

- **`purity` accepts any string, not just percentages:** `purity="USP Grade"` renders correctly in the purity badge. No validation is applied — the badge component treats it as a display value. This handles solvents, reference standards, and other products where a percentage isn't the right descriptor.

## Confirmed Working

- ✅ In-stock card renders with no corner badge — clean default state
- ✅ Low stock card renders with amber `low_stock` badge in corner
- ✅ Out-of-stock card renders with gray `out_of_stock` badge in corner and disabled ghost "Notify Me" CTA
- ✅ Sale card renders with green `sale` badge in corner, struck original price, green sale price below
- ✅ Sale + low stock card: `sale` wins corner, `low_stock` does not appear
- ✅ Research summary renders as centered white text over blurred image on hover; absent entirely when prop not passed
- ✅ `line-clamp-2` confirmed via TB-500 long name — does not break card height in grid
- ✅ Purity and batch badges visible at all times — not part of hover state
- ✅ `batchNumber` omitted: batch badge does not render, badge group shows purity only
- ✅ Skeleton matches card dimensions — `aspect-[4/3]` image block, two-line name, correct footer shape
- ✅ Badge demo on design page includes `sale` variant at both sm and xs sizes
- ✅ Footer updated to TASK-1-001 through TASK-1-008

## Important Notes

- **Research badge belongs on product detail pages, not cards.** Every product is research-only — showing it on every card in a grid is noise. Reserve it for the individual product page where the compliance context is more deliberate.
- **`in_stock` badge variant still exists in `badge.blade.php`.** It may be needed in other contexts (order summaries, admin panels, etc). It is simply not used on the card. Do not remove it.
- **Corner badge priority is `sale` > `low_stock` > `out_of_stock` > nothing.** Do not invert this. A sale product that's also low stock should surface the sale — it's the more actionable signal and the price display already communicates the discount. The low stock warning lives in the footer CTA behavior (button still routes to the product page, not disabled).
- **`originalPrice` is the sale trigger — no separate boolean.** Pass `original-price="$25.95"` and `price="$18.95"` together. The component infers sale state from `originalPrice` being non-null. Do not add a `sale` boolean prop — it would create a second source of truth.
- **Sale badge in corner when `originalPrice` is set, even if `batchStatus` is `low_stock`.** This is intentional. The sale badge wins regardless of stock state (unless out of stock, where the CTA disables and the out_of_stock context is already clear from the disabled button).
- **`font-mono-data` font-size conflicts with Tailwind text utilities.** `font-mono-data` sets `font-size: 0.875rem` in CSS directly — it will override any `text-*` size utility on the same element. Always apply it to a child `<span>` or ensure the element doesn't need a non-body font size.
- **`x-transition` for repeated hover states, `animate-*` classes for one-shot entry animations.** The CSS animation classes fire once and are done. Use `x-transition` with Alpine `x-show` for anything driven by interactive state.
- **Stagger animation for the grid is a Phase 3 concern.** The card component is stagger-ready (`--stagger-index` custom property from TASK-1-004 can be applied at the call site), but the card itself has no stagger logic. The parent grid loop handles it.

## Blockers Encountered

None.

## Configuration Changes

```
No configuration files modified.
```

## Next Steps

- TASK-1-009 — CoA Accordion; purity and batch badge values are established and ready to appear in the accordion header. The `font-mono-data` pattern for inline data values is confirmed working.
- Product detail page (Phase 2+) — Research Only badge and full compliance UI should appear here, not on the card.

## Files Created/Modified

- `resources/views/components/product/card.blade.php` — created — variant-driven product card (hover blur, corner badge, slash pricing, composed badges + buttons)
- `resources/views/components/product/card-skeleton.blade.php` — created — pulse placeholder matching card dimensions
- `resources/views/components/product/badge.blade.php` — modified — `sale` variant added
- `resources/views/design.blade.php` — modified — Product Card section added, badge demo updated with `sale` variant, footer caption updated to TASK-1-008

---
**For Next Claude:** Product cards use `<x-product.card>` with these key conventions: (1) `originalPrice` prop triggers sale treatment — slash display in footer + green Sale badge in corner; (2) corner badge priority is `sale` > `low_stock` > `out_of_stock` > nothing — in stock shows no corner badge; (3) Research Only badge does NOT appear on cards — it belongs on the product detail page; (4) `in_stock` badge variant still exists in badge.blade.php for other contexts but is not used on cards; (5) `purity` accepts any string, not just percentages. The sale badge variant (`bg-brand-success`, tag icon, white text) was added to `badge.blade.php` as part of this task — it was not present in TASK-1-007's output.
