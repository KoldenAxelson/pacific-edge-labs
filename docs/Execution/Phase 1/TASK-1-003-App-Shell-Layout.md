# [TASK-1-003] Base App Shell & Layout Primitives

## Objective
Update the app layout blade and build the structural components every page will use: container, section wrapper, page header, and grid. These are invisible scaffolding — built right, everything placed inside them automatically feels coherent.

## Deliverables

### Update `resources/views/layouts/app.blade.php`
- `<body>` class: `font-body bg-brand-bg text-brand-navy antialiased`
- Structure: header slot → disclaimer banner placeholder → `<main>` → footer slot
- Flash messages rendered at top of `<main>`
- Toast container rendered at end of `<body>`

### `resources/views/components/ui/container.blade.php`
Single max-width wrapper. Props: `size` (sm/default/lg/full). Default is `max-w-6xl`, lg is `max-w-7xl`. Always applies `mx-auto w-full px-4 sm:px-6 lg:px-8`.

### `resources/views/components/ui/section.blade.php`
Vertical rhythm wrapper. Props: `spacing` (tight/default/loose/hero). Maps to `py-8` through `py-20 md:py-32`.

### `resources/views/components/ui/page-header.blade.php`
Consistent page title area — white background, bottom border, inside a container. Props: `title`, `subtitle` (optional), `eyebrow` (optional — renders in small caps cyan above the title). Will gain a breadcrumb slot in Phase 2; design it to accommodate one without requiring it now.

### `resources/views/components/ui/grid.blade.php`
Responsive product grid. Props: `cols` (2/3/4). Maps to responsive column classes with `gap-6`. Default is 3 columns.

### `resources/views/components/ui/divider.blade.php`
Horizontal rule. Props: `spacing` (tight/default/loose). Uses `border-brand-border`.

## Acceptance Criteria
- [ ] Body background is `#F8F9FA` on all app routes, white on surfaces
- [ ] `<x-ui.container>` centers with correct max-width and horizontal padding on mobile
- [ ] `<x-ui.grid cols="3">` produces 1→2→3 column responsive layout
- [ ] Page header eyebrow renders in small-caps cyan
- [ ] All components accept additional classes via `$attributes->merge()`

## Notes
Subdirectory components (`ui/container`) auto-resolve as `<x-ui.container>` in Laravel 12 — no manual registration needed. Verify this works before proceeding.

The `page-header` eyebrow treatment (small caps, cyan, tight tracking) is going to be a recurring pattern across the site — product categories, section labels, badge text. Get it right here and it pays dividends everywhere.

---
**Sequence:** 3 of 15 — depends on TASK-1-001, TASK-1-002
**Estimated time:** 2–3 hours
