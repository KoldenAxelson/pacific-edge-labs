# [INFO-1-016] Product Card Polish - Completion Report

## Metadata
- **Task:** INFO-1-016-Product-Card-Polish
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-16
- **Duration:** ~2h (extra-curricular, outside formal task scope)
- **Status:** ✅ Complete

## What We Did

Reworked `x-product.card` from a tall, zone-heavy layout to a compact, Thorne-inspired card. Referenced Thorne's live shop page (screenshot) as the design benchmark. Updated `card-skeleton.blade.php` to mirror the new structure, and kept `design.blade.php` in sync throughout.

- Replaced `aspect-[4/3]` image ratio with `aspect-square` — biggest single win for mobile density
- Removed `batchNumber` prop and batch badge from the card entirely — batch ID belongs on the product detail page, not the grid card
- Replaced the separate `border-t` footer zone with a single unified content area — one padding zone, no visual break
- Moved price display onto the image as an overlaid pill (bottom-left), matching Thorne's pattern
- Added cart icon button overlaid bottom-right of image; `<a href>` → `<button type="button">` since navigating and adding to cart are different actions
- Replaced corner `sale` badge with stacked price pills (faded struck original + green current price) — the visual treatment is the signal, no badge needed
- Corner badge retained for `low_stock` and `out_of_stock` only; badge fades out (`opacity-0`) when the hover overlay is active so text isn't competing
- Replaced the research hover overlay (previously white text on dimmed image) with `blur-sm brightness-50` on the image + dark navy text overlaid — readable without a frosted box
- Split hover overlay into `research-tagline` (bold) + `research-summary` (regular weight), matching Thorne's bold-then-body hover pattern
- Overlay animation: asymmetric fade-from-top — 500ms ease-out enter, 300ms ease-in leave, `-translate-y-2` start position
- Out-of-stock CTA: replaced static disabled bell with Alpine toggle — `notified: false` in `x-data`, bell outline ↔ green check fill on click
- Notify button uses `border-2 border-brand-navy` outline (matching `x-ui.button variant="outline"` weight) rather than a thin `border-1`
- Both icon buttons sized `w-10 h-10` (40px touch target) with `w-4.5 h-4.5` icons
- Updated `design.blade.php` prop table, demo cards, code-block examples, and section comments throughout to reflect all changes

## Deviations from Plan

This was unplanned exploratory work, so no formal task document existed. Deviations are relative to initial direction given during the session.

- **Tooltip removed:** Originally implemented a floating Alpine tooltip (above card, z-10 stacking, `pointer-events-none`) but reverted at user request — the blur overlay approach was preferred
- **Sale corner badge removed:** Initial rework kept the `sale` variant badge in the corner. Replaced with stacked price pills overlaid on the image — cleaner, less badge clutter
- **`researchTagline` prop added mid-session:** Not in the original spec; added when the Thorne bold-then-body hover pattern was requested. Fully backward-compatible — overlay renders with either or both props present

## Confirmed Working

- ✅ `aspect-square` renders correctly at all three grid breakpoints (1 / 2 / 3 col)
- ✅ Price pill: cyan for regular, green + faded struck for sale — verified in design page demo grid
- ✅ Corner badge fades with `transition-smooth` when `hovered` becomes true — same timing as the blur
- ✅ `out_of_stock` notify button toggles between bell outline and green check via `notified` Alpine state
- ✅ Hover overlay enter (500ms) and leave (300ms) are asymmetric and feel deliberate, not snappy
- ✅ `card-skeleton.blade.php` structure matches new card exactly — square image, bottom bar placeholders, single content zone
- ✅ `design.blade.php` prop table and demo cards have no stale `batch-number` references
- ✅ `image-src` and `image-alt` props documented in design page (were missing from original prop table)

## Important Notes

- **`notified` state is local Alpine only.** It resets on page reload. A Livewire persist layer (or localStorage via Alpine `$persist`) needs to be wired when the notification signup backend is built. The comment in the component flags this.
- **`overflow-hidden` is back on the outer wrapper.** An earlier iteration moved it to the image container only (to allow a floating tooltip to escape). It's restored to the outer wrapper now that the overlay is inset. If a carousel or similar container clips cards, this is fine — the blur overlay is fully inset.
- **The `sale` badge variant in `badge.blade.php` is untouched.** It's still valid for use elsewhere in the UI (e.g. a product detail page hero). The card just no longer uses it.
- **Cart button is a `<button>`, not an `<a>`.** Wire it with `wire:click="addToCart({{ $productId }})"` or an Alpine `$dispatch` event when building the cart phase. The `href` prop is still available on the card for wrapping the image or title as a link.
- **`researchTagline` is optional.** The overlay renders if either `researchTagline` or `researchSummary` is non-null. Cards with only a summary still work; cards with neither get no overlay and no blur on hover.

## Blockers Encountered

- **Blocker:** Thorne's shop page (`/products`) is a JS-rendered SPA — `web_fetch` returned nav shell only, no product card markup. → **Resolution:** Used `image_search` to surface screenshots and worked from a user-provided screenshot of the actual hover state.
- **Blocker:** `str_replace` requires exact string match — file already existing caused one collision when re-creating card.blade.php. → **Resolution:** Deleted the existing draft with `rm` before creating fresh.

## Configuration Changes

No config files modified. All changes are in Blade component files only.

## Next Steps

- Wire `x-heroicon-o-shopping-cart` button to cart Livewire action when cart phase is built
- Replace `notified` local Alpine state with `Alpine.$persist` or a Livewire event for real notification opt-in
- Consider adding a `'new'` case to the `$cornerVariant` logic in `card.blade.php` once the `new` flag is available on the Product model
- Update any existing product grid views that pass `batch-number` to `x-product.card` — the prop no longer exists and will silently pass through `$attributes` without rendering

## Files Created/Modified

- `resources/views/components/product/card.blade.php` — modified — full rework of layout, hover, badges, and CTAs
- `resources/views/components/product/card-skeleton.blade.php` — modified — updated to mirror new card structure
- `resources/views/design.blade.php` — modified — demo cards, prop table, section labels, and code-block examples updated

---
**For Next Claude:** The card component has two hover text props now — `research-tagline` (bold, short phrase) and `research-summary` (regular weight, one sentence). Both are optional but you need at least one for the blur overlay to activate at all. The `notified` Alpine state on out-of-stock cards is purely local — flagged in an inline comment for Livewire wiring later. Do not re-introduce `batch-number` to the card; it was deliberately moved to the product detail page.
