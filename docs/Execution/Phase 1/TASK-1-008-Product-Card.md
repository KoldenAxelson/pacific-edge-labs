# [TASK-1-008] Product Card with Hover Reveal

## Objective
Build the product card — the most-seen component on the site. White surface, 1px border, structured information hierarchy, and the Thorne-inspired hover effect: the image area blurs and a one-line research summary fades in over it. Purity percentage is always visible, never hidden behind a hover state.

## Design Direction
The hover blur is an image-area-only effect, not a full-card overlay. The card structure below the image (purity, price, status, CTA) stays visible and readable at all times. The hover is a reward for the browser, not the primary information delivery mechanism.

The research summary text that appears on hover follows a strict template to keep all cards consistent:
> *"[Compound] has been studied for [mechanism] in [research context]."*

Example: *"BPC-157 has been studied for tissue repair and gut lining regeneration in preclinical animal models."*

This keeps it informative, non-hyped, and compliant. Whoever populates products in Phase 2 should have this template as guidance.

## Deliverables

### `resources/views/components/product/card.blade.php`

**Structure:**
1. Image area (aspect-[4/3], relative, overflow-hidden)
   - Product image or beaker placeholder
   - Hover overlay: `group-hover` applies `backdrop-blur-sm` + darkening to image, research summary fades in over it
   - Research Only badge — absolute positioned top-right corner, always visible
2. Card body
   - Category eyebrow (small caps, muted)
   - Product name (DM Sans h4, navy, `line-clamp-2`)
   - Purity row: label + value in `font-mono-data` cyan — **always visible, never hidden**
   - Batch row: label + value in `font-mono-data` neutral (if provided)
3. Card footer (separated by top border)
   - Price (DM Sans)
   - Stock status badge
   - CTA button (primary for in_stock, disabled ghost for out_of_stock)

**Props:** `name`, `category`, `price`, `purity`, `batchNumber`, `batchStatus` (in_stock/low_stock/out_of_stock), `href`, `imageSrc`, `imageAlt`, `researchSummary` (the hover text).

**Hover behavior (Alpine `x-data="{ hovered: false }"`):**
- `@mouseenter="hovered = true"` / `@mouseleave="hovered = false"`
- Image wrapper: `:class="{ 'blur-sm brightness-50': hovered }"` with `transition-smooth`
- Summary overlay: `x-show="hovered"` with `x-transition` opacity fade, absolutely positioned over image area, centered text in white, body-sm

**Border behavior:** `border border-brand-border` at rest, `hover:border-brand-cyan` on hover. This pairs with the image blur — the entire card "activates."

### `resources/views/components/product/card-skeleton.blade.php`
Pulse placeholder matching the card's exact structure. Used while products load in Phase 2.

## Acceptance Criteria
- [ ] Card at rest: clean, clinical, all information visible
- [ ] Hover: border turns cyan, image blurs, research summary fades in
- [ ] Research summary uses `animate-fade-in` from TASK-1-004 vocabulary
- [ ] Purity percentage is ALWAYS visible — never obscured by hover state
- [ ] Research Only badge always visible in image corner
- [ ] Out of stock: CTA is disabled ghost button, "Notify Me"
- [ ] Cards in a 3-col grid have consistent heights (flexbox column layout)
- [ ] Skeleton pulses and matches card dimensions exactly

## Notes
If no `researchSummary` prop is passed, the hover overlay should not appear (image still blurs on hover, but no text). This gracefully handles cases during Phase 2 seeding where summary text hasn't been written yet.

The stagger animation for grid cards comes in Phase 3 (shop page) when cards are rendered in a loop. The individual card component doesn't need to know about stagger — the parent grid handles it via the `--stagger-index` custom property from TASK-1-004.

---
**Sequence:** 8 of 15 — depends on TASK-1-001 through TASK-1-007
**Estimated time:** 3–4 hours
