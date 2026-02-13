# Phase 1 — Design System & Brand Foundation
## Execution Index

---

## The Vision in One Sentence
Thorne's clinical authority, with a cyan accent where it matters, and enough animation polish that the site *feels* intentional without ever calling attention to itself.

---

## Design Decisions (Locked)

| Decision | Choice |
|---|---|
| Aesthetic direction | Thorne-inspired clinical. White surfaces, structured, data-forward. |
| Primary color | Deep Navy `#0F172A` |
| Accent color | Cyan `#06B6D4` — used sparingly at high-value moments only |
| Background | Off-white `#F8F9FA` |
| Surface treatment | White + 1px solid border. No shadows. |
| Compliance color | Amber — research badges and attestation UI only |
| Headings | DM Sans 600 |
| Body | Inter |
| Data values | JetBrains Mono (purity %, batch numbers, order IDs) |
| Button shape | Pill / fully rounded |
| Card hover effect | Image area blurs, one-line research summary fades in |
| CoA display | Expandable accordion, collapsed by default |
| Research badge | Amber, prominent, on every product |
| Age gate | Minimal card on blurred navy overlay, no dismiss |
| Animation philosophy | Two-phase choreography: container first, content follows with delay |
| `/design` page | Clinical document, three sections (Clinical / Professional / Polish), all animations live-triggerable |

---

## Task Sequence

Execute in order. Each task depends on the ones before it.

```
TASK-1-001  Color Palette & CSS Variables         1–2 hrs
TASK-1-002  Typography Configuration              1–2 hrs
TASK-1-003  App Shell & Layout Primitives         2–3 hrs
TASK-1-004  Animation Vocabulary                  1–2 hrs
TASK-1-005  Button Components                     2–3 hrs
TASK-1-006  Form Elements                         3–4 hrs
TASK-1-007  Badge Components                      1–2 hrs
TASK-1-008  Product Card with Hover Reveal        3–4 hrs
TASK-1-009  CoA Accordion Component               2–3 hrs
TASK-1-010  Compliance UI Components              3–4 hrs
TASK-1-011  Alerts & Toast Notifications          2–3 hrs
TASK-1-012  Navigation Header & Footer            4–5 hrs
TASK-1-013  /design Route & Showcase Page         4–6 hrs
TASK-1-014  Component Reference Docs              2–3 hrs
TASK-1-015  Polish Pass                           2–4 hrs
─────────────────────────────────────────────────────────
Total estimated                                 33–50 hrs
```

---

## File Inventory at Phase 1 Completion

```
resources/
├── css/
│   ├── app.css                         ← tokens, animations, base styles
│   └── colors.md                       ← internal color reference
├── js/
│   └── app.js                          ← Alpine + Collapse plugin
└── views/
    ├── components/
    │   ├── coa/
    │   │   ├── accordion-list.blade.php
    │   │   ├── card.blade.php
    │   │   └── summary-strip.blade.php
    │   ├── compliance/
    │   │   ├── age-gate.blade.php
    │   │   ├── attestation-set.blade.php
    │   │   └── disclaimer-banner.blade.php
    │   ├── design/
    │   │   ├── code-block.blade.php
    │   │   ├── color-swatch.blade.php
    │   │   ├── prop-table.blade.php
    │   │   ├── section.blade.php
    │   │   └── subsection.blade.php
    │   ├── product/
    │   │   ├── badge.blade.php
    │   │   ├── badge-group.blade.php
    │   │   ├── card.blade.php
    │   │   └── card-skeleton.blade.php
    │   └── ui/
    │       ├── alert.blade.php
    │       ├── button.blade.php
    │       ├── button-group.blade.php
    │       ├── container.blade.php
    │       ├── divider.blade.php
    │       ├── flash-messages.blade.php
    │       ├── footer.blade.php
    │       ├── grid.blade.php
    │       ├── icon-button.blade.php
    │       ├── nav-link.blade.php
    │       ├── page-header.blade.php
    │       ├── section.blade.php
    │       ├── toast.blade.php
    │       ├── toast-container.blade.php
    │       └── form/
    │           ├── checkbox.blade.php
    │           ├── group.blade.php
    │           ├── input.blade.php
    │           ├── radio.blade.php
    │           ├── select.blade.php
    │           └── textarea.blade.php
    ├── layouts/
    │   ├── app.blade.php               ← updated structure
    │   ├── guest.blade.php             ← fonts added
    │   └── navigation.blade.php        ← replaced entirely
    └── design.blade.php                ← /design showcase

tailwind.config.js                      ← brand colors, fonts, type scale
routes/web.php                          ← /design route added
package.json                            ← @alpinejs/collapse added
POLISH-NOTES.md                         ← created in TASK-1-015
```

---

## Phase 1 Sign-off Criteria
Before calling Phase 1 complete and moving to Phase 2 (Product Catalog):

- [ ] `/design` renders all 10 sections without errors
- [ ] Every animation in the Polish section is live-triggerable
- [ ] Product card hover blur works, purity always visible
- [ ] CoA accordion two-phase choreography is smooth
- [ ] Age gate shows on first load, enters with animation
- [ ] Disclaimer banner present on every page
- [ ] Navigation header/footer renders correctly on mobile
- [ ] Zero database queries on `/design` page
- [ ] Reduced motion preference disables all animations
- [ ] `POLISH-NOTES.md` documents all timing decisions

---

## Next Phase
**Phase 2 — Product Catalog & Pages**
Database models, Filament admin CRUD, public shop page (with card stagger animation), product detail pages, CoA section, search with benefit-based tagging.

The `researchSummary` prop on product cards needs content — before Phase 2 seeding, establish the copy template: *"[Compound] has been studied for [mechanism] in [research context]."* Every product needs one before the hover effect has anything to show.
