# [TASK-1-010] Compliance UI Components

## Objective
Build the age gate modal, persistent disclaimer banner, and checkout attestation set. These components are non-negotiable — they exist as much for payment processor reviewers as for end users. They must be visually obvious and structurally inescapable.

## Design Direction
Age gate: minimal centered card on a blurred navy overlay. No close button. No click-outside-to-dismiss. The only exits are "I am 21+ — Enter" or "I am under 21 — Exit (leaves site)." The blurred background is `backdrop-filter: blur(8px)` over `rgba(15, 23, 42, 0.75)` — deep navy tint, brand-appropriate.

## Deliverables

### `resources/views/components/compliance/age-gate.blade.php`
Alpine `x-data="{ verified: false }"`, `x-show="!verified"`, `x-cloak`.

Card structure:
- Dark navy header bar: beaker icon in cyan circle, "Age Verification Required" eyebrow in cyan small-caps, site name in white DM Sans
- Body: explanation paragraph, amber info box ("Research Use Only — not for human consumption"), confirmation note about logging
- Two buttons: primary cyan full-width "I am 21 or older — Enter Site" (`@click="verified = true"`), ghost small full-width "I am under 21 — Exit" (links to google.com)
- Footer note: "Your confirmation is logged for compliance purposes." — small, faint, but present

Phase 4 adds cookie/session persistence. For now `verified = false` means it always shows.

### `resources/views/components/compliance/disclaimer-banner.blade.php`
Persistent strip. Props: `variant` (page-top/footer), `compact` (bool).

- Amber background (`bg-brand-amber-bg`), amber border
- Beaker icon + "Research Use Only" label in amber small-caps
- One-line disclaimer: "All products sold strictly for laboratory research. Not for human consumption."
- Compact variant omits the secondary sentence

Add to `app.blade.php` just below the navigation.

### `resources/views/components/compliance/attestation-set.blade.php`
The grouped checkout checkboxes. Amber-background card with document-check icon header, "Research Attestation Required" title, explanatory sentence, then four compliance checkboxes using `<x-ui.form.checkbox compliance>`:

1. "I am a qualified researcher or research professional."
2. "I confirm these products are for research purposes only — not for human consumption."
3. "I confirm I am 21 years of age or older."
4. "I agree to the Terms of Service, Research Use Policy, and Privacy Policy."

Props: `errors` array (for Phase 4 server-side validation display). All four must be checked before checkout proceeds — enforcement is Phase 4, visual structure is here.

## Acceptance Criteria
- [ ] Age gate has NO close button and NO click-outside dismissal
- [ ] `x-cloak` prevents flash before Alpine initializes
- [ ] Blurred overlay covers entire viewport
- [ ] "Exit" button navigates away from site
- [ ] "Enter" button dismisses modal (`verified = true`)
- [ ] Disclaimer banner renders in amber on both `page-top` and `footer` variants
- [ ] Disclaimer banner is integrated into `app.blade.php` layout
- [ ] Attestation set shows 4 amber compliance checkboxes
- [ ] Attestation section header clearly says "Required"

## Notes
The age gate `verified = false` always-show behavior is intentional for Phase 1 demo — it means every page visit shows the gate. This is actually fine for a client demo. Phase 4 replaces this with cookie/session logic.

The amber treatment on the attestation set should feel like a different zone on the checkout page — something slightly more serious. The amber is doing the work that a red warning would do on a less refined site.

---
**Sequence:** 10 of 15 — depends on TASK-1-004, TASK-1-005, TASK-1-006
**Estimated time:** 3–4 hours
