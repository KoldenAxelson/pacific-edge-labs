# [INFO-1-011] Alert & Notification Components - Completion Report

## Metadata
- **Task:** TASK-1-011-Alerts-Toasts
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-13
- **Duration:** ~2h
- **Status:** ✅ Complete

## What We Did

- Created `resources/views/components/ui/alert.blade.php`: Four-variant inline alert (`info`, `success`, `warning`, `error`). Optional `title` prop renders bold above body text. Optional `dismissible` prop adds `x-data="{ show: true }"` and an X button that sets `show = false` — both live on the same outer div so the click handler is always inside the active Alpine scope (no hidden-listener problem). `info` variant uses `bg-brand-cyan-subtle` — see Deviations.
- Created `resources/views/components/ui/flash-messages.blade.php`: Reads `session('success')`, `session('error')`, `session('warning')`, `session('info')`, and `$errors->any()`. Each renders as a dismissible `x-ui.alert`. The entire block is conditionally rendered — no empty markup on pages with no messages. Validation errors render as a titled error alert with a `<ul>` of all messages.
- Created `resources/views/components/ui/toast.blade.php`: Server-rendered auto-dismiss notification for Phase 4/5 Livewire use. Props: `variant`, `message`, `duration` (default 4000ms). `x-init="setTimeout(() => show = false, duration)"` fires on mount. Enter: `animate-reveal-bottom`. Leave: `transition-medium` + `opacity-0` fade. Dark `bg-brand-navy` base with coloured accent icon per variant. `role="status"` `aria-live="polite"`.
- Created `resources/views/components/ui/toast-container.blade.php`: Fixed bottom-right, `z-50`, `w-80`, `pointer-events-none` on container / `pointer-events-auto` on individual toasts. Alpine manages a `toasts[]` array with `add()` and `dismiss()` methods. `window._showToast(variant, message, duration?)` is the public API, registered via `document.addEventListener('alpine:initialized', ...)` in a `<script>` tag using `Alpine.$data(mount)`. Icons are inlined as raw SVG paths — see Deviations.
- Modified `resources/views/layouts/app.blade.php`: Replaced the old `@if(session('status'))` raw flash block with `<x-ui.flash-messages />` at the top of `<main>`. Replaced the old `#toast-container` placeholder div with `<x-ui.toast-container />` before `</body>`.
- Modified `resources/views/design.blade.php`: Added Alerts & Toasts section demonstrating all four alert variants, titled alerts, two dismissible alerts, a flash-messages explanation card, static toast visual demos (`:duration="999999"` to keep them visible), and four live trigger buttons calling `window._showToast()`. Footer updated to TASK-1-001 through TASK-1-011.

## Deviations from Plan

- **`info` variant uses `bg-brand-cyan-subtle`, not `bg-brand-info-bg`:** The task spec listed `bg-brand-info-bg` but this token does not exist in the design system — it is absent from the Tailwind config, the CSS token set, and the design page color palette. Using a non-existent token produces no background silently. `bg-brand-cyan-subtle` is the correct semantic equivalent already in use across the system. If `brand-info-bg` is added as a token in a future task, the one-line swap is in `alert.blade.php`'s `match` block with a comment pointing here.

- **`window._showToast` registered via `alpine:initialized` script, not `x-init`:** Task spec did not specify the registration mechanism. First attempt used `x-init="window._showToast = (variant, message, duration) => add(variant, message, duration)"`. In Alpine v3, if `x-init` returns a function, Alpine calls it as a cleanup/init callback. An assignment expression evaluates to the assigned value — the arrow function — so Alpine called `add(undefined, undefined, undefined)` on every page load, producing a blank dark-navy toast that dismissed after 4 seconds. Resolution: removed `x-init` entirely, registered `window._showToast` in a `<script>` tag via `document.addEventListener('alpine:initialized', () => { Alpine.$data(mount).add(...) })`. No return value, nothing for Alpine to call.

- **Icons in `toast-container` are inlined SVG, not `<x-heroicon-o-*>`:** First attempt used Blade heroicon components inside `<template x-for>`. Blade renders these as `<svg>` elements (foreign content). Some browsers apply the adoption agency algorithm and push foreign content's parent `<div>` out of the `<template>` document fragment into the live DOM, producing a blank styled box in the bottom-right corner on every page load. Resolution: all four icons replaced with inline SVG paths (Heroicons v2 outline, 24×24 viewBox). Do not revert — this is documented in the block comment inside the component.

## Confirmed Working

- ✅ All 4 alert variants render with correct icon and semantic color
- ✅ `title` prop renders bold above body text
- ✅ Dismissible alert hides instantly on X click — no stale listener issue
- ✅ Flash messages component renders session values as styled dismissible alerts
- ✅ Validation errors render as a titled error alert with a bullet list
- ✅ `<x-ui.flash-messages />` wired into `app.blade.php` — old raw flash block removed
- ✅ Toast enters with `animate-reveal-bottom`, leaves with opacity fade
- ✅ Toast auto-dismisses after 4 seconds (default)
- ✅ Static toast demo on design page uses `:duration="999999"` — persists for inspection
- ✅ Toast container is fixed bottom-right, `pointer-events-none` container, `pointer-events-auto` per toast
- ✅ `window._showToast('success' | 'error' | 'warning' | 'info', message, duration?)` works from design page buttons
- ✅ No blank toast on page load — `x-init` registration pattern removed
- ✅ Design page Alerts & Toasts section shows all variants and live triggers
- ✅ Footer updated to TASK-1-001 through TASK-1-011

## Known Issues

None.

## Blockers Encountered

- **`x-init` returning an arrow function caused a blank toast on every page load:** Alpine v3 calls the return value of `x-init` if it is a function. The assignment `window._showToast = () => add(...)` returns the arrow function, so Alpine called `add(undefined, undefined, undefined)` immediately. → **Resolution:** Removed `x-init`. Registered `window._showToast` in a `<script>` tag using `alpine:initialized` + `Alpine.$data(mount)`.

- **`<x-heroicon-o-*>` inside `<template x-for>` produced a blank box on page load:** Blade-rendered SVG is foreign content; some browsers push its parent `<div>` out of the `<template>` fragment into the live DOM. → **Resolution:** Replaced all four Blade heroicon calls with inline SVG paths inside the template. Documented in the block comment in `toast-container.blade.php`.

## Configuration Changes

```
No configuration files modified.
```

## Next Steps

- TASK-1-012 — next Phase 1 task
- Phase 4: Livewire components should call `window._showToast()` via `$dispatch('toast', { variant, message })` browser events, or call it directly in a `@script` block. The public API is stable.
- Phase 4: `<x-ui.toast>` (the server-rendered component) is available for Livewire-driven toast injection if preferred over the JS API.
- If `brand-info-bg` is added as a design token, swap the one line in `alert.blade.php`'s `info` match arm.

## Files Created/Modified

- `resources/views/components/ui/alert.blade.php` — created — four-variant dismissible inline alert
- `resources/views/components/ui/flash-messages.blade.php` — created — session flash → styled alerts
- `resources/views/components/ui/toast.blade.php` — created — server-rendered auto-dismiss toast
- `resources/views/components/ui/toast-container.blade.php` — created — fixed bottom-right Alpine toast queue with `window._showToast()` API
- `resources/views/layouts/app.blade.php` — modified — flash-messages component added to `<main>`, toast-container component added before `</body>`, old placeholder markup removed
- `resources/views/design.blade.php` — modified — Alerts & Toasts section added, footer updated to TASK-1-011

---
**For Next Claude:** Four components in `resources/views/components/ui/`: `alert`, `flash-messages`, `toast`, `toast-container`. Key conventions: (1) `window._showToast(variant, message, duration?)` is the public toast API — registered via `alpine:initialized` script, NOT `x-init` (Alpine v3 calls the return value of `x-init` if it is a function; an assignment returns the function; blank toast on every load). (2) Icons inside `toast-container`'s `<template x-for>` are inline SVGs — do NOT replace with `<x-heroicon-o-*>` Blade components; foreign SVG content breaks out of `<template>` fragments in some browsers. (3) `info` alert variant uses `bg-brand-cyan-subtle` — `bg-brand-info-bg` token does not exist in the design system. (4) `alert.blade.php` dismissible: `x-data` and `x-show` are on the same outer div — do not split them; the X button's `@click` handler must be inside the same Alpine scope. (5) `flash-messages` reads `session('success' | 'error' | 'warning' | 'info')` and `$errors->any()` — Phase 4 controllers use `return redirect()->with('success', '...')`.
