# [TASK-1-014] Component Reference Documentation

## Objective
Add collapsible code + prop documentation to the `/design` page. Every component gets a "Show usage" code block and a "Show props" table — both collapsed by default so they don't clutter the visual showcase. The `/design` page becomes both a visual demo environment and a developer reference.

## Design Direction
Documentation is secondary to demonstration — it should be opt-in, not in-your-face. Collapsed by default with a small "Show code" / "Show props" link beneath each component section. Opening either one doesn't shift the rest of the page dramatically — the content slides in with `x-collapse`.

## Deliverables

### `resources/views/components/design/code-block.blade.php`
Collapsible code display. Props: `label` (button text, default "Show usage").

Structure:
- Toggle button: small, muted, `code-bracket` icon + label + chevron
- `x-collapse` container holding a dark-background pre/code block
- Copy button in the code block header: copies `$refs.code.textContent` to clipboard, shows "Copied!" for 2s via `x-data`
- Code block header shows language label left, copy button right, on `bg-brand-navy-800`
- Code itself: `bg-brand-navy text-slate-200 p-4` — monospace, readable

No syntax highlighting library needed. Plain monospace on navy reads well enough for this use case.

### `resources/views/components/design/prop-table.blade.php`
Collapsible prop API table. Props: `props` (array of `['name', 'type', 'default', 'description']`).

Same toggle pattern as code-block. Table header: `bg-brand-surface-2`. Columns: Prop (cyan mono), Type (purple mono — IDE convention), Default (muted mono), Description (regular text). Rows divided by `border-brand-border`.

### Add to each section in `design.blade.php`
After each component family, add the documentation pair. Priority order if time is tight:

1. `<x-ui.button>` — most-used component
2. `<x-product.card>` — most complex component
3. `<x-product.badge>` — many variants to remember
4. `<x-coa.card>` — unique accordion behavior worth documenting
5. `<x-ui.form.checkbox compliance>` — the compliance variant is non-obvious
6. `<x-ui.alert>` — semantic variants
7. `<x-ui.container>` — layout primitive most likely to be misused

Example for buttons:
```blade
<x-design.code-block label="Show usage">
&lt;x-ui.button variant="primary" size="md" icon="shopping-cart"&gt;
  Add to Cart
&lt;/x-ui.button&gt;

&lt;x-ui.button href="/products" variant="outline"&gt;
  Browse Products
&lt;/x-ui.button&gt;

&lt;x-ui.button variant="primary" disabled&gt;
  Out of Stock
&lt;/x-ui.button&gt;
</x-design.code-block>

<x-design.prop-table :props="[
    ['name' => 'variant',  'type' => 'string',  'default' => 'primary', 'description' => 'primary | secondary | outline | danger | ghost'],
    ['name' => 'size',     'type' => 'string',  'default' => 'md',      'description' => 'sm | md | lg'],
    ['name' => 'href',     'type' => 'string',  'default' => 'null',    'description' => 'Renders as <a> when set'],
    ['name' => 'disabled', 'type' => 'bool',    'default' => 'false',   'description' => 'Disables all interaction'],
    ['name' => 'icon',     'type' => 'string',  'default' => 'null',    'description' => 'Heroicon name, renders left of label'],
    ['name' => 'iconEnd',  'type' => 'string',  'default' => 'null',    'description' => 'Heroicon name, renders right of label'],
]" />
```

## Acceptance Criteria
- [ ] `<x-design.code-block>` collapses/expands cleanly with `x-collapse`
- [ ] Copy button works and shows "Copied!" confirmation
- [ ] `<x-design.prop-table>` renders all columns for each prop
- [ ] Both components collapsed by default — design page is not cluttered
- [ ] Minimum 7 components documented (see priority list above)
- [ ] Purple type column follows IDE convention and feels intentional

## Notes
The copy-to-clipboard uses `navigator.clipboard.writeText()` — works in all modern browsers over HTTPS. No polyfill needed.

The code blocks contain escaped HTML (`&lt;` etc.) since they're documenting Blade syntax inside HTML. Make sure the examples are actually runnable code, not pseudocode — if someone copies and pastes a snippet it should work.

---
**Sequence:** 14 of 15 — depends on TASK-1-013
**Estimated time:** 2–3 hours
