# [TASK-1-007] Badge Components

## Objective
Build `<x-product.badge>` and `<x-product.badge-group>`. Small but important — the Research Only badge is a compliance signal on every product, and the purity badge is the brand's primary trust differentiator displayed in mono.

## Deliverables

### `resources/views/components/product/badge.blade.php`
Single component, variant-driven. Props: `variant`, `value` (for data badges), `label` (override default text), `size` (sm/xs).

Variants:
- **research** — amber bg/border, beaker icon, "Research Only". Appears on every product card and detail page. Prominent but tasteful.
- **purity** — cyan subtle bg, check-badge icon, value in `font-mono-data`. Cyan color. This is the most important badge on the site.
- **batch** — neutral surface-2 bg, no icon, value in `font-mono-data`. Secondary metadata, neutral not cyan — it's informational not differentiating.
- **in_stock** — emerald bg/border
- **low_stock** — amber bg/border
- **out_of_stock** — slate bg/border
- **category** — white bg, navy border, no icon
- **new** — cyan bg, white text

All variants: `rounded-full`, `inline-flex items-center gap-1`, font-medium.

### `resources/views/components/product/badge-group.blade.php`
`flex flex-wrap items-center` with configurable gap. No logic — just a layout wrapper for when multiple badges appear together.

## Acceptance Criteria
- [ ] Research badge renders with amber treatment and beaker icon
- [ ] Purity badge value renders in `font-mono-data` cyan — looks like data, not marketing
- [ ] Batch badge value renders in `font-mono-data` neutral — informational, not highlighted
- [ ] `size="xs"` is noticeably smaller (used in compact card contexts)
- [ ] Badge group displays badges with correct gap, wraps on narrow screens

## Notes
The visual hierarchy matters: purity (cyan) > research (amber) > batch (neutral). Someone scanning a card should immediately notice the purity number, then the compliance signal, then the batch detail. Color weight achieves this without any extra layout work.

---
**Sequence:** 7 of 15 — depends on TASK-1-001, TASK-1-002
**Estimated time:** 1–2 hours
