# [TASK-2-013] Phase 2 Polish Pass

## Overview
Final pass across all Phase 2 pages before sign-off. Fix rough edges, verify
sign-off criteria, test mobile layouts, confirm page performance, and update
the Phase 2 index with completion status.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 1–2 hrs
**Depends on:** All previous Phase 2 tasks
**Blocks:** Phase 3 kickoff

---

## Checklist: Sign-Off Criteria

Work through these in order. Mark each complete before moving to Phase 3.

### Database & Data
- [ ] `categories` table has 6 rows with correct slugs and sort_order
- [ ] `products` table has 30 rows — all active, all with `short_description` populated
- [ ] `product_research_links` seeded for Semaglutide, Tirzepatide, Retatrutide, TB-500
- [ ] BAC Water has `compare_price` set, all others have `compare_price = null` (except intended sales)
- [ ] `php artisan migrate:fresh --seed` runs without errors from scratch

### Routes
- [ ] `GET /products` → 200
- [ ] `GET /products/semaglutide-15mg` → 200
- [ ] `GET /products/nonexistent` → 404
- [ ] `GET /categories/glp-1-metabolic` → 200
- [ ] `GET /categories/nonexistent` → 404
- [ ] `GET /search` → 200
- [ ] `GET /search?q=sema` → 200 with Livewire pre-filled
- [ ] Admin `/admin/products` → accessible to admin users only
- [ ] Admin `/admin/categories` → accessible to admin users only

### Product Cards
- [ ] `researchSummary` prop populated on all cards across all pages
- [ ] Product card links to correct `/products/{slug}` URL
- [ ] Sale badge shows on BAC Water card
- [ ] No broken image placeholders (placeholder renders cleanly if no image)

### Product Detail Pages
- [ ] Breadcrumb: All Products → Category → Product Name
- [ ] Anchor jump nav present (visible on md+ screens)
- [ ] All 5 sections render: Overview, Specifications, Description, Research, CoA
- [ ] CoA section shows Phase 3 placeholder — not an error state
- [ ] Add to Cart button present but visually disabled
- [ ] Related products show ≤ 4 from same category
- [ ] Research links render citations correctly with external links

### Search
- [ ] Typing "sema" returns Semaglutide (≤ 300ms debounce)
- [ ] Typing "PEL-TB5" returns TB-500 (SKU search)
- [ ] Typing "lyophilized" returns multiple products (description search)
- [ ] < 2 characters shows "enter 2 characters" prompt
- [ ] Zero results shows friendly empty state
- [ ] URL updates to `?q=...` as user types

### SEO
- [ ] Every product page has unique `<title>` and `<meta name="description">`
- [ ] `<link rel="canonical">` present on all product and category pages
- [ ] Open Graph tags present on product pages
- [ ] JSON-LD Product schema present on product pages
- [ ] JSON-LD ScholarlyArticle present for Semaglutide (has research links)
- [ ] View source: no duplicate `<title>` tags

### Filament Admin
- [ ] Products list loads, search works, filters work
- [ ] Create product form validates required fields
- [ ] Edit product: changing `hero_title` on a category updates the frontend
- [ ] "View Page" action on categories opens correct frontend URL

### Responsive / Performance
- [ ] `/products` mobile layout: cards stack to single column
- [ ] `/products/{slug}` mobile: image stack above info, not side-by-side
- [ ] Category nav pills wrap on mobile without overflow
- [ ] Page load < 2 seconds (Chrome DevTools Network tab, no throttling)
- [ ] No N+1 queries (confirm `with('images', 'category')` eager loading in controllers)
- [ ] Laravel Telescope: no query counts > 10 on any product page

### Compliance
- [ ] `<x-compliance.disclaimer-banner>` visible on all product pages (confirm it's in app layout)
- [ ] "Research use only" language present in product descriptions
- [ ] No language implying human use anywhere in seeded descriptions

---

## Common Issues to Check

**Slug collisions** — run `Product::whereIn('slug', [...duplicates...])->count()` to confirm uniqueness.

**Missing `short_description`** — `Product::whereNull('short_description')->count()` should return 0.

**Image fallback** — confirm the no-image placeholder in `products/show.blade.php` renders cleanly in the `@else` branch.

**SQLite test failures** — if full-text GIN index migration wrapped in `if pgsql`, confirm tests still run.

**Livewire `#[Url]` attribute** — requires Livewire v3. Confirm `composer.json` has `livewire/livewire: ^3.0`.

---

## Post-Polish: Update Docs

Once all checkboxes are green:

1. Update `docs/Execution/Phase 2/PHASE-2-INDEX.md` sign-off checklist to all checked.
2. Create `docs/history/Phase-2-Completion.md` with a brief summary of what was built,
   any decisions that changed during implementation, and known issues deferred to Phase 3.
3. Confirm `docs/Execution/TASK-3-000-Overview.md` exists and is ready to kick off.

---

## Phase 3 Handoff Notes

Pass these to Phase 3:
- Product detail pages have CoA placeholder sections at `#coa` ready to receive `<x-coa.accordion-list>`
- `product_research_links` table is separate from batch data — no schema conflict
- The `product_images` table's `path` column expects S3 keys — no full URLs stored
- Filament `ProductResource` will need a new "Batches" relation manager in Phase 3
