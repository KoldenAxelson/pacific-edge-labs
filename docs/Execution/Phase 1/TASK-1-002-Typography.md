# [TASK-1-002] Typography Configuration

## Objective
Import and configure three fonts, define a consistent type scale, and set global base styles. Typography is the second biggest trust signal after color — DM Sans headings give personality, Inter body gives readability, JetBrains Mono signals data precision.

## Font Roles
- **DM Sans** — headings only (h1–h4, product names, hero text). Geometric, slight personality, strong at display sizes.
- **Inter** — everything else. Body copy, nav, labels, form inputs. Neutral and legible. Already used in Filament admin — consistency is a bonus.
- **JetBrains Mono** — data contexts only. Batch numbers, purity percentages in tables, order IDs. Not decorative — it signals "this is a precise value."

## Deliverables

### Font import — `resources/views/layouts/app.blade.php` and `guest.blade.php`
```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500&display=swap" rel="stylesheet">
```

### `tailwind.config.js` — fontFamily
```js
fontFamily: {
  heading: ['"DM Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
  body:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
  mono:    ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
},
```

### `resources/css/app.css` — base layer
Set `html` to Inter, `h1-h4` to DM Sans, and define the `.font-mono-data` utility class for inline data values.

Type scale to configure in Tailwind:
- `display` — 3rem, tight tracking, 700 weight. Hero sections only.
- `h1` through `h4` — 2.25rem down to 1.25rem, DM Sans 600
- `body-lg`, `body`, `body-sm` — 1.125/1/0.875rem, Inter
- `label` — 0.875rem, medium weight, 0.01em tracking. Form labels, section eyebrows.
- `caption` — 0.75rem. Timestamps, fine print, metadata.
- `mono` — 0.875rem, JetBrains Mono. For `.font-mono-data` class.

### Global base styles
```css
html { font-family: 'Inter'; background: var(--color-bg); color: var(--color-text); -webkit-font-smoothing: antialiased; }
h1,h2,h3,h4 { font-family: 'DM Sans'; font-weight: 600; color: var(--color-navy); }
[x-cloak] { display: none !important; }
```

## Acceptance Criteria
- [ ] DM Sans renders on all headings, Inter on body
- [ ] JetBrains Mono renders on any `.font-mono-data` element
- [ ] No layout shift from font loading (preconnect in place, `font-display: swap`)
- [ ] `[x-cloak]` rule in place for Alpine
- [ ] Type scale utilities functional (`text-h1`, `text-body-sm`, etc.)

## Notes
The `[x-cloak]` rule is here rather than its own task — it's one line and age gate depends on it. Putting it here means it's in place before any interactive components are built.

Heading letter-spacing: `-0.02em` at h1, tightening slightly as you go up. Geometric sans at display sizes feels better compressed. Don't apply negative tracking below h3.

---
**Sequence:** 2 of 15 — depends on TASK-1-001
**Estimated time:** 1–2 hours
