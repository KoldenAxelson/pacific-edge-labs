# [TASK-1-004] Animation Vocabulary

## Objective
Define the complete animation system before any interactive components are built. A small set of named CSS animation classes that get applied consistently across the entire site — so every accordion, every card reveal, every modal entrance has the same *feel* even though they're different components.

## Design Direction
The VisorPlate FAQ is the reference. The key insight is the **two-phase choreography**: the container animates first (height, via `x-collapse`), then the *content* animates after a short delay (fade + translate). Nobody consciously registers the stagger — they just feel the difference between this and a jarring toggle.

PEL runs on a light surface, so translations need to be subtler than on a dark background. Content sliding in from 16px left reads cleanly on a dark card; on white it needs to be closer to 10px. Keep movement tight.

## Deliverables

### `resources/css/app.css` — animation definitions

```css
/* ============================================================
   PEL Animation Vocabulary
   ============================================================ */

/* Reveal from left — content that appears after a container expands */
/* Used: CoA accordion content, FAQ answers, expandable sections */
@keyframes revealFromLeft {
  from { opacity: 0; transform: translateX(-10px); }
  to   { opacity: 1; transform: translateX(0); }
}
.animate-reveal-left {
  animation: revealFromLeft 400ms ease-out 150ms both;
}

/* Reveal from bottom — content that rises into view */
/* Used: modal content, drawer panels, mobile nav */
@keyframes revealFromBottom {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-reveal-bottom {
  animation: revealFromBottom 350ms ease-out 100ms both;
}

/* Scale in — elements that pop into existence */
/* Used: badges appearing, toast notifications, tooltips */
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-scale-in {
  animation: scaleIn 200ms ease-out both;
}

/* Fade in — purely opacity, no movement */
/* Used: overlay backgrounds, subtle content transitions */
@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}
.animate-fade-in {
  animation: fadeIn 300ms ease-out both;
}

/* Stagger utility — apply via inline style for grid items */
/* Usage: style="animation-delay: calc(var(--stagger-index) * 60ms)" */
/* Used: product cards loading into grid */
.animate-stagger {
  animation: revealFromBottom 400ms ease-out both;
}
```

### `resources/css/app.css` — transition utilities
```css
/* Consistent easing curves as utilities */
.transition-smooth  { transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1); }
.transition-medium  { transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1); }
.transition-slow    { transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1); }
```

### `package.json` + `resources/js/app.js`
Install and register the Alpine.js Collapse plugin. This powers the height animation that pairs with the content animations above:
```bash
sail npm install @alpinejs/collapse
```
```js
import Collapse from '@alpinejs/collapse'
Alpine.plugin(Collapse)
```

## Usage Reference
When you use these, the pattern is always:
1. `x-collapse` (or Alpine transition) handles the **container** — height, visibility
2. `animate-reveal-*` class (conditionally applied) handles the **content** — the delayed follow-through

Example with CoA accordion:
```html
<!-- Container: x-collapse handles height -->
<div x-show="open" x-collapse x-collapse.duration.400ms>
  <!-- Content: animate-reveal-left fires after container starts opening -->
  <div :class="open ? 'animate-reveal-left' : ''">
    ... CoA metadata ...
  </div>
</div>
```

## Acceptance Criteria
- [ ] All 5 animation classes defined and compilable
- [ ] `@alpinejs/collapse` installed and registered in `app.js`
- [ ] `x-collapse` directive available (test by putting a simple toggle on a test page)
- [ ] Animation durations feel intentional — not too fast (jittery) not too slow (sluggish)
- [ ] Animations work on mobile (no jank from reduced motion preferences — add `@media (prefers-reduced-motion: reduce)` to disable where needed)

## Notes
Add `@media (prefers-reduced-motion: reduce) { .animate-reveal-left, .animate-reveal-bottom, .animate-scale-in, .animate-stagger { animation: none; } }` — accessibility requirement, and also just good practice.

The stagger delay for grid cards is deliberately handled via CSS custom property (`--stagger-index`) set inline rather than a Tailwind class. This keeps it flexible — you can set the index from Blade's `@foreach` loop: `style="--stagger-index: {{ $loop->index }}"`.

---
**Sequence:** 4 of 15 — depends on TASK-1-001 through TASK-1-003
**Estimated time:** 1–2 hours
