# [TASK-1-006] Form Elements

## Objective
Build the complete form component set: group wrapper, text input, textarea, select, checkbox, and radio. These cover auth flows, checkout, profile, and the compliance attestation UI at checkout.

## Design Direction
Outlined style — visible 1px border at rest, cyan border + ring on focus. Rounded `rounded-xl` (not pill — forms need to feel precise, not bouncy). Error state is red border. The compliance checkbox variant uses amber background to visually distinguish it from ordinary form fields.

## Deliverables

### `resources/views/components/ui/form/group.blade.php`
Wrapper that orchestrates label + input + error/hint. Props: `label`, `for`, `error`, `hint`, `required`. Required fields show a red `*` that is `aria-hidden` (the `required` attribute handles accessibility).

### `resources/views/components/ui/form/input.blade.php`
Standard text input. Props: `error` (applies red border state), `leading` (slot for prefix icon/text), `trailing` (slot for suffix). Base: `w-full rounded-xl border bg-white px-4 py-2.5 text-body-sm`. Focus: `focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan/20 focus:outline-none`.

### `resources/views/components/ui/form/textarea.blade.php`
Same border/focus treatment as input. Props: `error`, `rows` (default 4). `resize-y` allowed, not `resize-x`.

### `resources/views/components/ui/form/select.blade.php`
Custom chevron-down icon replaces browser default arrow (`appearance-none` + absolutely positioned heroicon). Same border/focus treatment.

### `resources/views/components/ui/form/checkbox.blade.php`
Two modes via `compliance` prop:
- **Standard** — plain flex layout, small checkbox, label beside it
- **Compliance** — wraps entire element in amber `bg-brand-amber-bg border border-brand-amber-border rounded-xl p-4`. Used for all research attestation checkboxes. The amber background is a deliberate compliance signal — it draws the eye to these checkboxes and makes them feel distinct from ordinary form fields.

Props: `label`, `description` (secondary text under label), `error`, `compliance`.

### `resources/views/components/ui/form/radio.blade.php`
Same pattern as standard checkbox. Props: `label`, `description`.

## Acceptance Criteria
- [ ] Focus state shows cyan ring on all inputs
- [ ] Error state shows red border + error message with warning icon below
- [ ] Compliance checkbox renders with amber background and distinct styling
- [ ] Select has custom chevron (no browser default arrow)
- [ ] All inputs have correct padding — minimum 44px tap target height
- [ ] `font-mono-data` can be passed as a class to input (for batch number fields)
- [ ] Required asterisk is `aria-hidden="true"`

## Notes
The compliance checkbox amber treatment is intentional and should feel slightly alarming — not panic-level, but "this is different from a normal checkbox." That distinction is what signals to payment processor reviewers that these are meaningful attestations, not decorative boxes.

---
**Sequence:** 6 of 15 — depends on TASK-1-005
**Estimated time:** 3–4 hours
