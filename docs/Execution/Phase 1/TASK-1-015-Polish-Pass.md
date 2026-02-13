# [TASK-1-015] Polish Pass — Animation & Interaction Refinement

## Objective
Revisit TASK-1-004 (animation vocabulary) and TASK-1-008 (product card hover) with everything now built and visible. This is the calibration pass — durations that looked right on paper may feel slightly off once you're interacting with real content in a real layout. Fix what needs fixing, and add any micro-interactions that couldn't be defined before the components existed.

## What This Task Is Not
This is not new features. It's not adding components. It's the difference between a site that works and a site that *feels right*. The kind of thing where 350ms vs 300ms on the card hover actually matters because you've now hovered it 50 times during development.

## Checklist of Things to Evaluate

### Animation timing calibration
Work through the `/design` page interacting with every animated element. For each one, ask: does it feel snappy enough, or sluggish? Is the delay before content appears correct, or does it create an awkward gap?

Specific things to check:
- [ ] CoA accordion: 400ms height collapse + 150ms content delay — does the stagger feel natural or disconnected?
- [ ] Mobile nav panel: `animate-reveal-bottom` — does it open fast enough for a nav? Nav interactions should feel snappier than content reveals (consider 250ms instead of 350ms)
- [ ] Card hover blur: transition duration on the blur — too fast feels jarring, too slow feels laggy. 200ms is the target but verify.
- [ ] Toast entrance: `animate-reveal-bottom` — feels right for a notification? Or should it slide in from the right edge (more conventional for bottom-right toasts)?
- [ ] Button `active:scale-[0.98]`: duration should be very fast (100ms) — if it's using `transition-smooth` (200ms) it may feel delayed. Consider `transition-all duration-100` for this specific effect.
- [ ] Age gate entrance: currently no entrance animation. Consider adding a `scale-in` or `reveal-bottom` to the card itself when the modal mounts. Without it the modal feels abrupt.

### Hover state consistency audit
Every interactive element on the `/design` page should have a visible hover state. Walk through:
- [ ] Nav links
- [ ] Footer links
- [ ] Icon buttons
- [ ] Card borders
- [ ] CoA accordion header
- [ ] Badge (does it need a hover state? Probably not.)
- [ ] Compliance checkbox label (cursor: pointer should be there)

### Micro-interaction additions
Small things that couldn't be specified before the components existed:

**Chevron on CoA accordion:** The rotation should use `transition-smooth` (200ms) not the default Tailwind transition. Verify the class is actually applied.

**Nav link active state transition:** When the route changes (will matter more in Phase 2), the active pill shouldn't hard-cut. Ensure `transition-colors` is on nav links.

**Cart badge:** The `0` badge is static, but its presence should be visually deliberate. Make sure it has a slight `ring-2 ring-white` to separate it from the icon — a small detail that looks intentional.

**Form focus states:** The cyan ring on focus should appear without any layout shift. Verify `focus:ring-offset-0` or `focus:ring-offset-2` is consistent and doesn't cause input height changes.

**Purity percentage in cards:** This is the most important data point on the card. Consider if `tabular-nums` should be applied to the mono value so that percentages with different digit counts align vertically in a grid.

### Reduced motion compliance
Run through `/design` with `prefers-reduced-motion: reduce` active in browser devtools. Every animation should be disabled. Verify the `@media (prefers-reduced-motion: reduce)` block from TASK-1-004 covers everything that was added in subsequent tasks.

## Deliverables
No new files. Changes are surgical edits to existing component files and `app.css`. Document each change in a short `POLISH-NOTES.md` in the project root — a running log of what was changed and why. This becomes useful reference when Phase 2 adds new components and needs to match the established feel.

## Acceptance Criteria
- [ ] Every animation duration has been consciously chosen, not defaulted
- [ ] Age gate modal has an entrance animation
- [ ] Toast slides correctly — direction and duration feel right for a notification
- [ ] Button press effect is fast (≤100ms)
- [ ] `tabular-nums` applied to purity percentage values
- [ ] Cart badge has ring separation from icon
- [ ] Reduced motion disables all animations
- [ ] `POLISH-NOTES.md` documents every change made in this task

## Notes
"Polish" is the hardest phase to define but the easiest to feel. The test is: show the `/design` page to someone who hasn't seen it and watch where their eyes go. If anything draws attention for the wrong reason (too slow, too abrupt, misaligned) that's what needs fixing. The goal is a site where nothing stands out — where every interaction just feels natural and expected.

This task is last for a reason. You can't calibrate animations in isolation. You need the full system built and running to know what feels right.

---
**Sequence:** 15 of 15 — depends on TASK-1-013, TASK-1-014
**Estimated time:** 2–4 hours (hard to predict — depends on how much needs adjusting)
