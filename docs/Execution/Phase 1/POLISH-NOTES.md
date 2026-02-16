# POLISH-NOTES.md
## Phase 1 Polish Pass — TASK-1-015

Running log of every change made during the final animation and interaction calibration pass. Referenced when Phase 2 adds new components that need to match the established feel.

---

### app.css — `animate-reveal-right` added

**What:** New keyframe (`revealFromRight`) and class. Slides in from `translateX(16px)`, 300ms, no delay.

**Why:** Bottom-right anchored toast notifications should enter from the right edge, not from below. Entering from below (`animate-reveal-bottom`) is the motion language for modals and drawers — things that rise from behind the page. Entering from the right is the convention for edge-anchored notifications (iOS, Gmail, etc.). The distinction matters because users have trained expectations about motion direction and element origin.

**Use on:** Toast notifications. Do not use on modals, drawers, or content reveals.

---

### app.css — `animate-reveal-nav` added

**What:** Reuses `revealFromBottom` keyframe at 250ms with no delay (vs. `animate-reveal-bottom`'s 350ms with 100ms delay).

**Why:** Navigation panels must feel immediately responsive. The 100ms `both` fill delay in `animate-reveal-bottom` creates a perceptible hesitation that reads as lag specifically in nav contexts, even though it feels correct for content reveals. The class exists for future use — the current sidebar uses translate-x and doesn't need it.

**Use on:** Any bottom-reveal nav pattern introduced in Phase 2+.

---

### app.css — reduced-motion block updated

**What:** Added `animate-reveal-right` and `animate-reveal-nav` to the `@media (prefers-reduced-motion: reduce)` block.

**Why:** Every animation class must be in this block. Two new classes were added in this pass, so the block needed to be kept in sync.

---

### button.blade.php — `active:duration-100` on filled variants

**What:** Added `active:duration-100` to primary, secondary, and danger variants. Ghost and outline variants are unaffected (they don't scale).

**Why:** `transition-smooth` (200ms) was applying to all transitions including `active:scale-[0.98]`. A 200ms press-down feels like nothing happened — the finger is off the button before the animation completes. 100ms snaps immediately and registers as a physical response. Hover colour transitions remain at 200ms because `active:duration-100` only applies during the `active` pseudo-state.

**Calibration note:** 100ms is the ceiling for press feedback. Below ~80ms it becomes imperceptible; above ~150ms it starts to feel like a lag rather than a response. 100ms is the industry standard.

---

### toast-container.blade.php — `x-transition:enter` fixed + direction corrected

**What:** Two changes: (1) fixed enter animation never firing, (2) swapped class from `animate-reveal-bottom` to `animate-reveal-right`.

**Why (enter not firing):** `add()` was pushing toasts with `show: true`. Alpine creates the DOM element from `x-for` and immediately evaluates `x-show` as `true` — there is no reactive `false → true` change to observe, so `x-transition:enter` never intercepts it. Leave worked because `dismiss()` explicitly sets `show = false` on an already-live element. Fix: push with `show: false`, then flip in `$nextTick` after Alpine finishes the render cycle.

**Pattern to follow for all `x-for` + `x-show` + `x-transition:enter`:**
```js
this.items.push({ id, ..., show: false });
this.$nextTick(() => {
    const item = this.items.find(i => i.id === id);
    if (item) item.show = true;
});
```

**Why (direction):** See `animate-reveal-right` note above.

---

### toast.blade.php — `x-transition:enter` direction corrected

**What:** Swapped `x-transition:enter="animate-reveal-bottom"` to `animate-reveal-right`.

**Why:** The static toast renders on the design page for visual reference. They should use the same entrance direction as the live toasts in the container.

---

### age-gate.blade.php — entrance animation added

**What:** `x-transition:enter="animate-fade-in"` on the backdrop div; `:class="!verified ? 'animate-scale-in' : ''"` on the modal card div.

**Why:** Without an entrance animation the modal is a hard visual cut from nothing to a full dark blurred overlay — reads as a page glitch or rendering error. The backdrop fade (300ms) and card scale (200ms) together produce a deliberate, controlled appearance that signals "this is intentional" rather than "something broke."

**Alpine note:** The `:class` binding fires on any `verified → false` transition, including external calls via `window._showAgeGate()`, not just on page load. This is the correct approach — Alpine is observing the reactive state, not a one-time DOM event.

**Why not `x-transition` on the card?** `x-transition` requires a corresponding `x-show` or `x-if` on the same element. The card is always in the DOM — only the parent backdrop div has `x-show`. The `:class` pattern fires `animate-scale-in` each time the gate shows, which is the intended behaviour.

---

### coa/card.blade.php — `tabular-nums` on purity hero value

**What:** Added `tabular-nums` utility class to the `<dd>` holding the large purity percentage.

**Why:** The purity hero uses `font-mono` + `text-2xl` + `font-semibold`. It cannot use `font-mono-data` because that class forces `font-size: 0.875rem`, which would override `text-2xl`. `tabular-nums` was missing — without it, `99.9%` and `98.4%` have slightly different widths because proportional digits render at variable widths. In a grid of CoA cards, the purity values should align vertically. `tabular-nums` fixes this.

**Rule going forward:** Any mono value with a custom font size needs `font-mono` (family) + `tabular-nums` (numeric rendering) applied separately. `font-mono-data` is only for values at the standard 0.875rem data size.

---

### navigation.blade.php — `ring-2 ring-white` on cart badge

**What:** Added `ring-2 ring-white` to the cart badge `<span>`.

**Why:** The badge sits at `-top-0.5 -right-0.5` overlapping the corner of the cart icon. Without a ring, the cyan circle bleeds into the icon strokes at the overlap point and reads as part of the icon rather than a separate counter. The white ring creates a 2px visual separation — the same technique used by iOS, Gmail, and most notification badge systems. Small detail, but it's the difference between a badge that looks deliberate and one that looks stuck to the wrong element.

**Note:** Uses `ring-white` (static white) not `ring-offset-white`. The header background is always white, so the ring colour matches without needing offset mechanics.

---

*End of Phase 1 polish log.*
