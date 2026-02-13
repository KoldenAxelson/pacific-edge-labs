# [TASK-1-001] Color Palette & CSS Variables

## Objective
Define the complete PEL color system as CSS custom properties and Tailwind config extensions. Every subsequent task pulls from these tokens — no raw hex values anywhere after this.

## Design Direction
Thorne-inspired clinical base. White surfaces, deep navy as the anchor, cyan as the single pop of color. Amber reserved exclusively for compliance/research signals. Nothing else.

## Deliverables

### `resources/css/app.css` — add to `:root`
```css
:root {
  /* Core */
  --color-navy:         #0F172A;
  --color-navy-800:     #1E293B;
  --color-navy-700:     #334155;
  --color-navy-600:     #475569;
  --color-navy-500:     #64748B;

  /* Accent — use sparingly, high-value moments only */
  --color-cyan:         #06B6D4;
  --color-cyan-dark:    #0891B2;
  --color-cyan-light:   #67E8F9;
  --color-cyan-subtle:  #ECFEFF;

  /* Surfaces */
  --color-bg:           #F8F9FA;
  --color-surface:      #FFFFFF;
  --color-surface-2:    #F1F5F9;

  /* Borders */
  --color-border:       #E2E8F0;
  --color-border-dark:  #CBD5E1;

  /* Text */
  --color-text:         #0F172A;
  --color-text-muted:   #64748B;
  --color-text-faint:   #94A3B8;

  /* Semantic */
  --color-success:      #10B981;
  --color-success-bg:   #ECFDF5;
  --color-warning:      #F59E0B;
  --color-warning-bg:   #FFFBEB;
  --color-error:        #EF4444;
  --color-error-bg:     #FEF2F2;

  /* Compliance — amber, for research badges and attestation UI only */
  --color-amber:        #D97706;
  --color-amber-bg:     #FEF3C7;
  --color-amber-border: #FCD34D;
}
```

### `tailwind.config.js` — extend theme colors
Map all tokens to `brand-*` Tailwind utilities so components can use `bg-brand-navy`, `text-brand-cyan`, etc.

Note: if the project uses Tailwind v4 CSS-first config, define these via `@theme` directive in `app.css` instead. Check which approach is active before implementing.

### `resources/css/colors.md`
Brief internal reference doc. For each color note: the token name, hex, and its intended use. Include a one-line WCAG contrast note for the main pairings (navy on white, cyan on navy, amber on white).

## Acceptance Criteria
- [ ] `sail npm run dev` compiles without errors
- [ ] All CSS variables present in `:root`
- [ ] Tailwind `brand-*` utilities available
- [ ] No raw hex values anywhere in the codebase after this task
- [ ] Every token has a single documented purpose

## Notes
Cyan is the only expressive color in the entire palette. Don't dilute it by using it everywhere — it should signal "this matters" when it appears. Purity percentages, active nav states, primary CTAs, the beaker in the logo mark. That's it.

---
**Sequence:** 1 of 15 — everything depends on this
**Estimated time:** 1–2 hours
