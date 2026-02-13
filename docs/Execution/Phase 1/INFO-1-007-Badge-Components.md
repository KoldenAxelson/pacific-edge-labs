# [INFO-1-007] Badge Components - Completion Report

## Metadata
- **Task:** TASK-1-007-Badge-Components
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-13
- **Duration:** ~30m
- **Status:** ✅ Complete

## What We Did

- Created `resources/views/components/product/badge.blade.php`: single variant-driven component. Props: `variant`, `value`, `label`, `size`. All eight variants (`research`, `purity`, `batch`, `in_stock`, `low_stock`, `out_of_stock`, `category`, `new`) resolved via a single `match($variant)` that returns a config array containing `classes`, `icon`, `text`, and `mono` keys. Each variant owns its complete class string — no string appending, no conflicts. Purity and batch values rendered inside a nested `<span class="font-mono-data">` rather than on the badge element itself, keeping the size scale (`text-body-sm` / `text-caption`) authoritative while only overriding font-family.
- Created `resources/views/components/product/badge-group.blade.php`: pure layout wrapper. Single `gap` prop accepting a full Tailwind class string (e.g. `gap="gap-2"`, `gap="gap-1.5"`). No logic.
- Added Badge Components section to `resources/views/design.blade.php`: research badge (default + xs + custom label), purity badge (three values), batch badge, all six stock/category/new variants at both sizes, explicit sm-vs-xs size comparison panel, and three badge-group compositions representing standard card, compact card, and new product with category.
- Updated design page footer caption from TASK-1-006 to TASK-1-007.
- Created `resources/views/components/product/` directory (did not exist prior to this task).

## Deviations from Plan

- **`gap` prop takes a full Tailwind class string, not a bare integer:** The task spec described a configurable gap without specifying the API shape. Accepting a raw value (e.g. `gap="2"`) and assembling `"gap-{$gap}"` dynamically would produce a class string not present in source files, which Tailwind's content scanner cannot detect and will purge in production. The fix is to accept the full utility class as the prop value (`gap="gap-2"`) so the string appears literally in the template. This is the same lesson from TASK-1-006's padding issue.

- **`mono` rendered in a nested `<span>`, not on the badge element:** Applying `font-mono-data` to the outer badge element would override the font for the icon glyph as well (no practical effect since SVG icons ignore font-family, but semantically wrong). More importantly, the badge's `text-body-sm` / `text-caption` size classes must remain authoritative — `font-mono-data` should only override font-family, not cascade into unexpected specificity conflicts. A nested `<span>` is the clean solution.

## Confirmed Working

- ✅ Research badge renders with amber bg/border and beaker icon — visually consistent with compliance checkbox amber treatment
- ✅ Purity badge value renders in `font-mono-data` cyan — reads as data, not marketing copy
- ✅ Batch badge value renders in `font-mono-data` neutral/muted — informational weight, not highlighted
- ✅ `size="xs"` is noticeably smaller than `size="sm"` — confirmed via side-by-side size comparison panel
- ✅ Badge group wraps correctly at narrow widths — `flex-wrap` confirmed on design page
- ✅ `gap` prop on badge-group correctly controls spacing — `gap-1.5` (compact) vs `gap-2` (default) visually distinct
- ✅ Stock variants (in_stock / low_stock / out_of_stock) render with correct icons and color treatment
- ✅ Category and new badges render correctly without icons
- ✅ `label` prop overrides default text on research and new variants
- ✅ Design page footer updated to TASK-1-007

## Important Notes

- **`<x-product.badge>` not `<x-ui.badge>`:** Badges live under the `product` component namespace, not `ui`. They are product-domain components, not generic UI primitives. Do not move them to `ui/` — the `product/` namespace will grow to include card, badge, CoA accordion, and other product-specific components.
- **`gap` prop on badge-group must be a full Tailwind class string.** Pass `gap="gap-2"` not `gap="2"`. Assembling class strings dynamically from partial values defeats Tailwind's static content scanning and causes purging in production. This rule applies to any prop that feeds into a class attribute.
- **`font-mono-data` is applied inside a nested `<span>`, not on the badge wrapper.** This keeps size utilities on the outer element authoritative and avoids font-family leaking into unexpected places. Follow this pattern for any future badge-like component that mixes mono data with surrounding non-mono text.
- **Visual hierarchy: purity (cyan) > research (amber) > batch (neutral).** Do not elevate batch to cyan or reduce research to neutral. The color weight is the only mechanism enforcing scan-order hierarchy on product cards — no extra layout work is required as long as color assignments are respected.
- **`low_stock` correctly uses amber** — it maps to `brand-amber-bg` / `brand-amber-border` / `text-brand-amber`, the same palette as the compliance/research treatment. This is intentional: amber signals "attention required" consistently across the UI. It does not carry the compliance connotation of the `compliance` checkbox prop.
- **No `transition-smooth` on badges.** Badges are static display elements, not interactive controls. Adding hover transitions would imply interactivity. If a badge is ever used as a filter trigger (TASK-1-008 or later), the interactive treatment should be added at the call site, not baked into the component.
- **`product/` directory must be created before deploying.** `resources/views/components/product/` did not exist prior to this task. Run `mkdir -p resources/views/components/product/` before copying component files.

## Blockers Encountered

None.

## Configuration Changes

```
No configuration files modified.
```

## Next Steps

- TASK-1-008 — Product Card; will compose `<x-product.badge-group>` with multiple `<x-product.badge>` variants inside the card. The sm/xs size split maps directly to card vs compact-card contexts established in this task.
- TASK-1-009 — CoA Accordion; purity and batch badge values should appear in the accordion header — the components are ready.

## Files Created/Modified

- `resources/views/components/product/badge.blade.php` — created — variant-driven badge component (8 variants, 2 sizes)
- `resources/views/components/product/badge-group.blade.php` — created — flex-wrap layout wrapper
- `resources/views/design.blade.php` — modified — Badge Components section added, footer caption updated to TASK-1-007

---
**For Next Claude:** Badges live at `<x-product.badge>` and `<x-product.badge-group>` — `product` namespace, not `ui`. The `gap` prop on badge-group takes a full Tailwind class string (`gap="gap-2"`), not a bare number. Purity and batch values go in the `value` prop; the mono rendering is handled internally. Research badge is the compliance signal — amber, beaker icon, "Research Only" — and must appear on every product card and detail page. Visual hierarchy is enforced by color alone: purity cyan, research amber, batch neutral. Do not alter variant color assignments without a deliberate design decision. The `product/` component directory was created in this task and will grow through TASK-1-008 and TASK-1-009.
