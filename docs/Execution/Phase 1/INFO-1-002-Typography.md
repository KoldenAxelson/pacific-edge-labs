# [INFO-1-002] Typography Configuration - Completion Report

## Metadata
- **Task:** TASK-1-002-Typography
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-12
- **Duration:** ~30m
- **Status:** ✅ Complete with Notes

## What We Did

- Swapped font import in `resources/views/layouts/app.blade.php` and `guest.blade.php` from Figtree to the three-font bundle (DM Sans, Inter, JetBrains Mono) via Bunny Fonts with `display=swap`
- Added `heading`, `body`, and `mono` font family entries to `tailwind.config.js`; also updated `sans` from Figtree to Inter so existing `font-sans` classes resolve correctly
- Added `--text-*` type scale tokens to the existing `@theme` block in `app.css` (no new block created)
- Added `@layer base` to `app.css` with global `html` styles (Inter, `var(--color-bg)`, `var(--color-text)`, antialiasing) and `h1–h4` styles (DM Sans, weight 600, `var(--color-navy)`, `line-height: 1.15`)
- Applied heading letter-spacing per task notes: `-0.02em` on h1, `-0.015em` on h2, `-0.01em` on h3, none on h4 and below
- Added `.font-mono-data` utility class with JetBrains Mono, `font-variant-numeric: tabular-nums`, and zeroed letter-spacing

## Deviations from Plan

- **`font-variant-numeric: tabular-nums` added to `.font-mono-data`:** Not in the task spec but strongly implied by the use case (batch numbers, purity percentages, order IDs). Tabular nums prevent columns from shifting width as values change. Low-risk addition, easy to remove if unwanted.

- **`sans` font family also updated:** The task spec only defines `heading`, `body`, and `mono`. Both blade layouts use `font-sans` on `<body>` — leaving `sans` pointed at Figtree would mean the base layer's `font-family: Inter` applies but `font-sans` silently reverts to a missing font. Updated `sans` to Inter to keep them consistent. Figtree is no longer referenced anywhere.

## Confirmed Working

- ✅ Font import updated identically in both blade layouts — verified by reading output files
- ✅ `display=swap` present on font link — no layout shift from blocking font load
- ✅ `preconnect` to `fonts.bunny.net` retained in both layouts
- ✅ All three `--text-*` scale tokens in `@theme`, all six type sizes covered (display, h1–h4, body-lg, body, body-sm, label, caption, mono)
- ✅ Letter-spacing applied only to h1–h3 per task notes — h4 and below untouched
- ✅ Existing `[wire\:loading]`, `[x-cloak]`, and all TASK-1-001 tokens preserved unchanged
- ⚠️ Fonts not visually verified in browser — no runtime access. Must be confirmed with `sail npm run dev` + visual check before closing.

## Important Notes

- **`bg-gray-100` still on both blade layout wrappers.** These use raw Tailwind default gray, not brand tokens. They'll be replaced when TASK-1-003 (App Shell Layout) rebuilds the outer shell. Not a problem now — flagged so it doesn't get forgotten.
- **`font-body` and `font-heading` utilities are now available** via `tailwind.config.js` for explicit overrides in components. Base layer handles the defaults — these are available when you need to buck the default (e.g. a heading-weight label, a body-font callout).
- **`.font-mono-data` is a plain CSS class**, not a Tailwind utility. Apply it directly in Blade: `<span class="font-mono-data">{{ $batchNumber }}</span>`. No `@apply` needed.

## Blockers Encountered

- None.

## Configuration Changes

```
File: resources/views/layouts/app.blade.php
Changes: Replaced Figtree font link with DM Sans + Inter + JetBrains Mono bundle

File: resources/views/layouts/guest.blade.php
Changes: Replaced Figtree font link with DM Sans + Inter + JetBrains Mono bundle

File: tailwind.config.js
Changes: Updated fontFamily — replaced sans: [Figtree] with sans/heading/body/mono

File: resources/css/app.css
Changes: Added --text-* tokens to @theme block; added @layer base with html and h1–h4 global styles; added .font-mono-data utility class
```

## Next Steps

- Run `sail npm run dev` and do a visual spot-check that DM Sans renders on headings and Inter on body before starting TASK-1-003
- TASK-1-003 (App Shell Layout) — can now use `font-heading`, `font-body`, `font-mono`, and all `text-*` scale utilities; also the right time to replace `bg-gray-100` on the blade layout wrappers with `bg-brand-bg`

## Files Created/Modified

- `resources/views/layouts/app.blade.php` — modified — updated font import
- `resources/views/layouts/guest.blade.php` — modified — updated font import
- `tailwind.config.js` — modified — added heading/body/mono font families, updated sans
- `resources/css/app.css` — modified — type scale tokens, base layer, `.font-mono-data`

---
**For Next Claude:** Project is Tailwind v4 — type scale lives in `@theme` as `--text-*` tokens in `app.css`, not in `tailwind.config.js`. Font families still live in `tailwind.config.js` (v4 doesn't yet have a clean `@theme` equivalent for `fontFamily`). Base layer global styles are in `@layer base` in `app.css`. `.font-mono-data` is a plain CSS class for data values — use it on batch numbers, purity percentages, order IDs. The `bg-gray-100` wrappers in both blade layouts are a known leftover — TASK-1-003 is the right place to address them.
