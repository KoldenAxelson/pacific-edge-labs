# [INFO-1-004] Animation Vocabulary - Completion Report

## Metadata
- **Task:** TASK-1-004-Animation-Vocabulary
- **Phase:** 1 (Design System)
- **Completed:** 2026-02-12
- **Duration:** ~1h
- **Status:** ✅ Complete with Notes

## What We Did

- Appended the full PEL Animation Vocabulary block to `resources/css/app.css`: five keyframe definitions (`revealFromLeft`, `revealFromBottom`, `scaleIn`, `fadeIn`) with their utility classes, plus `.animate-stagger` with the `--stagger-index` custom property already wired into the `animation-delay` calc
- Added three transition utilities (`.transition-smooth / medium / slow`) using the shared `cubic-bezier(0.4, 0, 0.2, 1)` curve
- Added `@media (prefers-reduced-motion: reduce)` block disabling all five animation classes and all three transition utilities
- Added `@alpinejs/collapse` to `package.json` `devDependencies`, version-matched to `alpinejs` (`^3.4.2`)
- Wired up full Alpine initialization in `resources/js/app.js`: import Alpine → import Collapse → `Alpine.plugin(Collapse)` → `window.Alpine = Alpine` → `Alpine.start()`
- Added an Animation Vocabulary section to `resources/views/design.blade.php`: x-collapse smoke test toggle, five animation class reference cards (fire on page load), and three transition utility hover cards

## Deviations from Plan

- **`animate-stagger` calc folded into the rule:** The task spec left `animation-delay: calc(var(--stagger-index) * 60ms)` as a usage comment only. It's been moved into the `.animate-stagger` rule with a `var(--stagger-index, 0)` default so the class works correctly without requiring the inline style to be set first.

- **`transition-smooth/medium/slow` scope extended:** The `prefers-reduced-motion` block also disables the transition utilities (`transition: none`), which the task spec didn't explicitly call out. Added since suppressing animations but leaving instant transitions feels inconsistent for users with that preference.

- **Animation replay buttons scrapped:** The design page initially included ↺ replay buttons per animation card. Restarting a CSS animation programmatically requires forcing a synchronous browser reflow (`element.offsetWidth`) between class removal and re-addition — too brittle for a reference page. Cards now fire once on page load; a reload replays them. A comment in `design.blade.php` explains why.

- **Double-Alpine investigation:** Alpine was previously removed from `app.js` due to a double-load warning. `bootstrap.js` and `app.blade.php` both came back clean — no CDN tag, no second import found. The original source of the warning was not identified. It did not reappear after re-adding Alpine initialization to `app.js`.

## Confirmed Working

- ✅ `x-collapse` smoke test on `/design` — panel height animates smoothly on toggle (not a snap), confirming the Collapse plugin registered correctly
- ✅ `animate-reveal-left` fires after the collapse container opens — two-phase choreography confirmed visually
- ✅ All five animation classes fire on page load in the reference cards
- ✅ Three transition utility cards show perceptibly different speeds on hover (200ms vs 300ms vs 400ms)
- ✅ `npm run dev` builds without errors after `rm -rf node_modules package-lock.json && npm install`
- ✅ `prefers-reduced-motion` block present and syntactically correct

## Important Notes

- **`--stagger-index` must be set inline from Blade.** The `.animate-stagger` rule uses `var(--stagger-index, 0)` — without the inline style, all grid cards animate simultaneously with zero delay. Set it in `@foreach` loops: `style="--stagger-index: {{ $loop->index }}"`.
- **Two-phase choreography pattern is now established.** Every future expandable component follows the same structure: `x-collapse` (or an Alpine `x-show` transition) on the container, `animate-reveal-*` conditionally applied to the content inside. Don't collapse these into one element.
- **The double-Alpine warning source was never found.** If it reappears, check any Blade views that aren't under `layouts/` — Breeze sometimes scatters CDN tags in auth views (`login.blade.php`, `register.blade.php`, etc.). Run `grep -r "alpinejs\|cdn.jsdelivr" resources/views/` to locate it.
- **Animation replay in the browser requires a reflow trick.** If replay is ever needed on the design page or elsewhere: remove the class, read `element.offsetWidth` synchronously, then re-add the class. `$nextTick` is not sufficient — it queues a microtask, not a reflow.

## Blockers Encountered

- **Rollup ARM64 module not found on Apple Silicon:** `npm run dev` failed immediately after installing `@alpinejs/collapse`. Unrelated to our changes — a pre-existing npm optional dependency bug. **Resolution:** `rm -rf node_modules package-lock.json && npm install && npm run dev`.

## Configuration Changes

```
File: resources/css/app.css
Changes: Appended PEL Animation Vocabulary (5 keyframes + utility classes), 3 transition utilities, prefers-reduced-motion block

File: resources/js/app.js
Changes: Added full Alpine initialization — import Alpine, import Collapse, Alpine.plugin(Collapse), window.Alpine, Alpine.start()

File: package.json
Changes: Added "@alpinejs/collapse": "^3.4.2" to devDependencies

File: resources/views/design.blade.php
Changes: Added Animation Vocabulary section — x-collapse smoke test, 5 animation reference cards, 3 transition hover cards; updated footer caption to TASK-1-004
```

## Next Steps

- TASK-1-005 — next in Phase 1 sequence; animation classes are now available for any interactive components it introduces
- Add `/design` to pre-production removal checklist (no auth, established in TASK-1-003, still applies)
- If the double-Alpine warning resurfaces, run `grep -r "alpinejs\|cdn.jsdelivr" resources/views/` before debugging further

## Files Created/Modified

- `resources/css/app.css` — modified — animation vocabulary, transition utilities, reduced-motion block appended
- `resources/js/app.js` — modified — full Alpine + Collapse plugin initialization
- `package.json` — modified — `@alpinejs/collapse` added to devDependencies
- `resources/views/design.blade.php` — modified — Animation Vocabulary section added

---
**For Next Claude:** Five animation utility classes are now live in `app.css`: `animate-reveal-left`, `animate-reveal-bottom`, `animate-scale-in`, `animate-fade-in`, `animate-stagger`. Three transition utilities: `transition-smooth` (200ms), `transition-medium` (300ms), `transition-slow` (400ms). All suppress under `prefers-reduced-motion`. The `x-collapse` directive is registered and working — confirmed on `/design`. The canonical choreography pattern: `x-collapse` on the container, `animate-reveal-left` (or `bottom`) on the content inside, applied conditionally when open. The `animate-stagger` class requires `--stagger-index` set inline from `$loop->index` in Blade foreach loops.
