# [TASK-1-013] `/design` Route & Showcase Page

## Objective
Build the `/design` route and its Blade file — a live component showcase and personal reference document. Not as sparse as Thorne (which has none), not as performative as a portfolio. A clean clinical document where every component is actually interactive, every animation is triggerable, and the decisions are visible.

## Design Direction
Three named sections reflecting the brand's three pillars:
- **Clinical** — the static decisions: colors, typography, spacing
- **Professional** — the structural decisions: components and layout
- **Polish** — the motion decisions: every animation triggerable live

The page itself uses the same design system it documents. It's self-referential and that's the point — it proves the system holds up under its own weight.

## Deliverables

### `routes/web.php`
```php
Route::get('/design', function () {
    return view('design');
})->name('design');
```
No auth gate — accessible for client demos.

### `resources/views/design.blade.php`
Uses `<x-app-layout>` (age gate will show on first load — that's fine for demo, it proves the gate works).

**Page header:** Navy background section, beaker icon, "Design System" title, "v1.0 — Pacific Edge Labs" subtitle. Same eyebrow/heading pattern as the rest of the site.

**Sticky anchor nav:** Below header, white bg, `z-30`. Scrollable row of anchor links to each section. On a site where the main header is `z-40`, the anchor nav sits just below.

**Sections (each uses `<x-design.section>` helper):**

1. **Colors** — color swatches grid. Each swatch: colored square, token name, hex value. Organized by family: Navy scale → Cyan scale → Surfaces → Semantic.

2. **Typography** — live text at each scale step. Display → h1 → h2 → h3 → h4 → body-lg → body → body-sm → label → caption → mono-data. Each shows the actual rendered text, not a description of it.

3. **Buttons** — all variants, all sizes, with icons, disabled states, button groups. Organized by sub-section.

4. **Forms** — a realistic mini-form: email input (normal), batch number input (`font-mono-data`), select, textarea, standard checkbox, compliance checkbox side by side.

5. **Badges** — all variants in both sizes. Organized in a flex-wrap grid.

6. **Product Cards** — 3-column grid showing in_stock / low_stock / out_of_stock states. Hover to trigger the blur/reveal. Skeleton row below.

7. **CoA Accordion** — accordion list with 2 cards (one open, one closed). Summary strip below.

8. **Compliance** — disclaimer banner (both variants), attestation set, and an age gate preview trigger button (see Notes).

9. **Alerts** — all 4 variants including a dismissible one. Toast trigger buttons below.

10. **Polish** *(this section is the point)* — live demos of every animation:
    - Accordion expand trigger
    - Card hover instruction ("hover the card above")
    - Toast trigger buttons for each variant
    - A standalone button with the `active:scale` effect called out explicitly
    - The stagger entrance: a "Replay" button that rerenders 6 cards with stagger delay applied

### `resources/views/components/design/section.blade.php`
Props: `id`, `title`, `subtitle`. Renders a section with `scroll-mt-32` (accounts for sticky header + anchor nav), title/subtitle header with bottom border, and a slot.

### `resources/views/components/design/subsection.blade.php`
Props: `title`. Small label + slot. Used within sections for organizing variants.

### `resources/views/components/design/color-swatch.blade.php`
Props: `bg` (Tailwind bg class), `label`, `hex`. Renders the colored square + token name + hex in mono.

## Age Gate in the Design Page
The age gate `verified = false` default means it shows on page load. This is actually ideal for demos — it proves the gate works. For the *design page section* showing the compliance components, add a separate button:

```html
<div x-data="{ showGate: false }">
    <x-ui.button @click="showGate = true" variant="outline">Preview Age Gate</x-ui.button>
    <template x-if="showGate">
        <div class="fixed inset-0 z-50 ..." style="backdrop-filter: blur(8px); ...">
            <x-compliance.age-gate />
            {{-- Design-only close wrapper — NOT part of the real age gate component --}}
            <button @click="showGate = false" class="absolute top-4 right-4 ...">
                Close Preview
            </button>
        </div>
    </template>
</div>
<p class="text-caption text-brand-text-muted mt-2">
    Note: close button shown for design preview only. Real age gate has no close.
</p>
```

## Acceptance Criteria
- [ ] `GET /design` returns 200 with no errors
- [ ] Anchor nav scrolls to each of the 10 sections
- [ ] All sections render without PHP errors (all components exist)
- [ ] Color swatches display all brand tokens with hex values
- [ ] Typography section shows every scale step as live rendered text
- [ ] Product card hover blur works in the cards section
- [ ] CoA accordion expands/collapses with correct choreography
- [ ] Toast trigger buttons fire toasts at bottom-right
- [ ] Stagger "Replay" button re-triggers card entrance animation
- [ ] Age gate preview opens and closes via design-page wrapper button
- [ ] Zero database queries (verify with Telescope or query log)
- [ ] Page renders cleanly on mobile

## Notes
The Polish section's stagger replay is a bit tricky — Alpine can't easily force a rerender. One approach: use a `key` counter that increments on "Replay" click, and wrap the card grid in `<template x-key="replayCount">`. Alternatively, toggle a class that removes/re-adds the animation. The exact mechanism matters less than the effect being demoable.

If Tailwind purges design-page-only classes (brand tokens used only in the swatch component), add the design blade to Tailwind's content array or use `@source` if on Tailwind v4.

---
**Sequence:** 13 of 15 — depends on ALL prior tasks
**Estimated time:** 4–6 hours
