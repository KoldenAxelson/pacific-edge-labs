# PEL Color Palette Reference

Internal reference for the Pacific Edge Labs design token system. All colors are defined as CSS custom properties in `resources/css/app.css` and exposed as `brand-*` Tailwind utilities. **No raw hex values anywhere else in the codebase.**

---

## Core — Navy

The structural anchor of the entire UI. Used for backgrounds, headers, text, and sidebar surfaces.

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-navy` | `brand-navy` | `#0F172A` | Primary dark surface, main background, body text |
| `--color-navy-800` | `brand-navy-800` | `#1E293B` | Sidebar, card headers, elevated dark surfaces |
| `--color-navy-700` | `brand-navy-700` | `#334155` | Secondary dark surfaces, dividers on dark bg |
| `--color-navy-600` | `brand-navy-600` | `#475569` | Muted text on dark backgrounds, icons |
| `--color-navy-500` | `brand-navy-500` | `#64748B` | Placeholder text, subtle labels on dark bg |

---

## Accent — Cyan

The only expressive color in the palette. Reserve it for moments that truly matter — purity percentages, active navigation state, primary CTAs, the beaker in the logo mark. Overuse destroys its signal value.

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-cyan` | `brand-cyan` | `#06B6D4` | Primary accent: active states, primary buttons, key data points |
| `--color-cyan-dark` | `brand-cyan-dark` | `#0891B2` | Hover/pressed state for cyan elements |
| `--color-cyan-light` | `brand-cyan-light` | `#67E8F9` | Decorative highlights, icon fills on dark backgrounds |
| `--color-cyan-subtle` | `brand-cyan-subtle` | `#ECFEFF` | Tinted backgrounds behind cyan UI (e.g. active nav row) |

---

## Surfaces

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-bg` | `brand-bg` | `#F8F9FA` | Page/app background |
| `--color-surface` | `brand-surface` | `#FFFFFF` | Card, modal, panel backgrounds |
| `--color-surface-2` | `brand-surface-2` | `#F1F5F9` | Nested surfaces, table row alternates, input backgrounds |

---

## Borders

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-border` | `brand-border` | `#E2E8F0` | Default border for cards, inputs, dividers |
| `--color-border-dark` | `brand-border-dark` | `#CBD5E1` | Stronger borders, focused input rings |

---

## Text

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-text` | `brand-text` | `#0F172A` | Primary body text |
| `--color-text-muted` | `brand-text-muted` | `#64748B` | Secondary labels, captions, metadata |
| `--color-text-faint` | `brand-text-faint` | `#94A3B8` | Placeholder text, disabled states, fine print |

---

## Semantic

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-success` | `brand-success` | `#10B981` | Success icons, positive status indicators |
| `--color-success-bg` | `brand-success-bg` | `#ECFDF5` | Success alert/badge backgrounds |
| `--color-warning` | `brand-warning` | `#F59E0B` | Warning icons, caution indicators |
| `--color-warning-bg` | `brand-warning-bg` | `#FFFBEB` | Warning alert/badge backgrounds |
| `--color-error` | `brand-error` | `#EF4444` | Error states, destructive actions, validation failures |
| `--color-error-bg` | `brand-error-bg` | `#FEF2F2` | Error alert/badge backgrounds |

---

## Compliance — Amber

**Amber is a restricted color.** Use it exclusively for research-tier indicators, compliance badges, and attestation UI. Appearing anywhere else dilutes its meaning as a signal of elevated scrutiny or third-party verification.

| Token | Utility | Hex | Intended Use |
|---|---|---|---|
| `--color-amber` | `brand-amber` | `#D97706` | Compliance badge text, research-tier icons |
| `--color-amber-bg` | `brand-amber-bg` | `#FEF3C7` | Compliance badge and attestation backgrounds |
| `--color-amber-border` | `brand-amber-border` | `#FCD34D` | Compliance badge borders |

---

## WCAG Contrast Notes

These are the primary pairings in the UI. All pass WCAG AA (4.5:1 minimum for normal text).

| Pairing | Ratio | Rating |
|---|---|---|
| Navy (`#0F172A`) on White (`#FFFFFF`) | **17.4:1** | AAA ✅ |
| White (`#FFFFFF`) on Navy (`#0F172A`) | **17.4:1** | AAA ✅ |
| Cyan (`#06B6D4`) on Navy (`#0F172A`) | **5.5:1** | AA ✅ |
| Amber (`#D97706`) on White (`#FFFFFF`) | **3.5:1** | AA Large / fail small* |
| Navy text on Surface-2 (`#F1F5F9`) | **15.3:1** | AAA ✅ |

*Amber on white fails WCAG AA for small text (ratio 3.5:1 < 4.5:1). Amber should only appear as a badge with a background (`--color-amber-bg`) or at large/bold weight. Never as small body text on white.

---

*Last updated: 2026-02-12 — TASK-1-001*
