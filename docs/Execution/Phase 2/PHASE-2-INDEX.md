# Phase 2 — Product Catalog & Pages
## Execution Index

---

## The Vision in One Sentence
A fast, SEO-optimized product catalog that surfaces scientific credibility (research summaries, purity data, disclaimers) on every page, using the Phase 1 design system components without modification.

---

## Context from Phase 1

Phase 1 delivered the complete design system. These components are ready to consume:

| Component | Location | Phase 2 Usage |
|---|---|---|
| `<x-product.card>` | `components/product/card.blade.php` | Category listing pages, "All Products" page |
| `<x-product.badge>` | `components/product/badge.blade.php` | Product detail page (purity, category, status) |
| `<x-product.badge-group>` | `components/product/badge-group.blade.php` | Product detail page badge row |
| `<x-product.card-skeleton>` | `components/product/card-skeleton.blade.php` | Loading states for product grids |
| `<x-ui.container>` | `components/ui/container.blade.php` | All page wrappers |
| `<x-ui.grid>` | `components/ui/grid.blade.php` | Product card layouts |
| `<x-ui.page-header>` | `components/ui/page-header.blade.php` | Category and product page headers |
| `<x-ui.button>` | `components/ui/button.blade.php` | "Add to Cart" (non-functional), search, filters |
| `<x-ui.section>` | `components/ui/section.blade.php` | Content sections on detail pages |
| `<x-compliance.disclaimer-banner>` | `components/compliance/disclaimer-banner.blade.php` | Already in app layout — appears on all pages |
| `<x-ui.form.input>` | `components/ui/form/input.blade.php` | Search bar |
| `<x-ui.form.select>` | `components/ui/form/select.blade.php` | Category filter dropdown |

**Debt from Phase 1:** The `researchSummary` prop on product cards needs content. Establish the copy template before seeding: *"[Compound] has been studied for [mechanism] in [research context]."*

---

## Key Decisions (Pre-Locked)

These decisions were made during Phase 2 planning (TASK-2-000-Overview.md) and are locked:

| Decision | Choice |
|---|---|
| Product count | ~30 SKUs now, scalable to 60–80 |
| Category structure | Flat (3–4 top-level categories, no nesting) |
| Categories | GLP-1 Agonists, Recovery & Healing, Performance & Growth, (possibly Niche) |
| Search strategy | PostgreSQL `ILIKE` on name + description (no Scout/Meilisearch) |
| URL structure | Clean slugs: `/products/semaglutide-15mg`, `/categories/glp-1-agonists` |
| Price storage | Decimal(10,2) in USD |
| Soft deletes | Yes — products are deactivated, never hard-deleted |
| Batch/CoA fields | NOT in this phase — placeholder sections on product pages only |
| Cart button | Rendered but non-functional until Phase 4 |
| Filament resources | Basic CRUD for products and categories in this phase |

---

## Decisions to Make with User

These need to be resolved at the start of Phase 2 via conversation:

1. **Product images** — Separate `product_images` table or JSON column on `products`?
2. **Research links** — Separate table or JSON column?
3. **Variant handling** — Are 5mg, 10mg, 15mg separate products or variants of one product?
4. **Product detail layout** — Tabs or single-page scroll for description/research/CoA sections?
5. **Search UX** — Search bar in header (always visible) or dedicated search page?
6. **Real-time search** — AJAX with debounce or full-page refresh?
7. **SEO approach** — Auto-generate meta from description or allow custom meta per product?
8. **Schema.org markup** — Implement Product structured data in this phase?
9. **Seed data** — Use real Pacific Edge product names/prices or generic placeholders?
10. **Category pages** — Custom content per category or just filtered product grids?

---

## Suggested Task Sequence

Execute in order. Each task depends on the ones before it.

```
TASK-2-001  Database Schema Design                    1–2 hrs
TASK-2-002  Create Migrations                         1–2 hrs
TASK-2-003  Product & Category Models                 2–3 hrs
TASK-2-004  Category Seeder                           1–2 hrs
TASK-2-005  Product Seeder with Realistic Data        2–3 hrs
TASK-2-006  Category Listing Page                     2–3 hrs
TASK-2-007  Product Detail Page                       3–4 hrs
TASK-2-008  "All Products" Page with Filters          2–3 hrs
TASK-2-009  Search Functionality                      2–3 hrs
TASK-2-010  Filament Product Resource                 2–3 hrs
TASK-2-011  Filament Category Resource                1–2 hrs
TASK-2-012  SEO Meta Tags & Structured Data           2–3 hrs
TASK-2-013  Phase 2 Polish Pass                       1–2 hrs
─────────────────────────────────────────────────────────────
Total estimated                                     22–35 hrs
```

**Note:** Task numbers are suggestions from TASK-2-000-Overview.md. Actual tasks will be generated after the user conversation resolves the open decisions above. The sequence and scope may change.

---

## Expected File Inventory at Phase 2 Completion

```
app/
├── Models/
│   ├── Product.php                    ← new
│   └── Category.php                   ← new
├── Filament/Resources/
│   ├── ProductResource.php            ← new
│   ├── ProductResource/Pages/         ← new (Create, Edit, List)
│   └── CategoryResource.php           ← new
│       └── CategoryResource/Pages/    ← new (Create, Edit, List)
├── Http/Controllers/
│   ├── ProductController.php          ← new
│   └── CategoryController.php         ← new

database/
├── migrations/
│   ├── xxxx_create_categories_table.php    ← new
│   ├── xxxx_create_products_table.php      ← new
│   └── xxxx_create_product_images_table.php ← new (if separate table decided)
├── factories/
│   ├── ProductFactory.php             ← new
│   └── CategoryFactory.php            ← new
├── seeders/
│   ├── CategorySeeder.php             ← new (replace placeholder)
│   └── ProductSeeder.php              ← new (replace placeholder)

resources/views/
├── products/
│   ├── index.blade.php                ← new ("All Products" page)
│   ├── show.blade.php                 ← new (product detail page)
│   └── partials/                      ← new (search bar, filter sidebar, etc.)
├── categories/
│   ├── show.blade.php                 ← new (category listing page)
│   └── partials/                      ← new (category header, etc.)

routes/
└── web.php                            ← modified (product/category routes added)

config/
└── (no changes expected)
```

---

## Database Schema Preview

Based on TASK-2-000-Overview.md decisions. Final schema depends on user conversation.

### categories
```
id              bigint PRIMARY KEY
name            varchar(100)
slug            varchar(100) UNIQUE
description     text NULLABLE
sort_order      integer DEFAULT 0
active          boolean DEFAULT true
created_at      timestamp
updated_at      timestamp
```

### products
```
id              bigint PRIMARY KEY
category_id     bigint FOREIGN KEY → categories.id
sku             varchar(30) UNIQUE
name            varchar(200)
slug            varchar(200) UNIQUE
description     text
research_summary varchar(500)       ← one-liner for card hover
price           decimal(10,2)
meta_title      varchar(200) NULLABLE
meta_description text NULLABLE
active          boolean DEFAULT true
created_at      timestamp
updated_at      timestamp
deleted_at      timestamp NULLABLE  ← soft deletes
```

**Indexes:** `category_id`, `sku`, `slug`, `active`, full-text on `name` + `description`

### product_images (if separate table)
```
id              bigint PRIMARY KEY
product_id      bigint FOREIGN KEY → products.id
path            varchar(500)        ← S3 path
alt_text        varchar(200)
sort_order      integer DEFAULT 0
created_at      timestamp
updated_at      timestamp
```

---

## Phase 2 Sign-off Criteria

Before calling Phase 2 complete and moving to Phase 3 (Batch & CoA System):

- [ ] `categories` table migrated with seed data (3–4 categories)
- [ ] `products` table migrated with seed data (10–15 realistic peptide products)
- [ ] Product and Category models with relationships, scopes, and accessors
- [ ] Category listing pages rendering product cards from database
- [ ] Product detail page with all content sections (description, research, placeholder CoA)
- [ ] "All Products" page with category filter
- [ ] Basic keyword search working on name + description
- [ ] Filament resources for product and category CRUD
- [ ] SEO meta tags on product and category pages
- [ ] Clean URL slugs (`/products/semaglutide-15mg`)
- [ ] "Add to Cart" button rendered (non-functional, ready for Phase 4)
- [ ] Research-only disclaimers visible on product pages
- [ ] Responsive on mobile/tablet
- [ ] Page loads under 2 seconds
- [ ] Product card `researchSummary` populated for all seeded products

---

## Next Phase
**Phase 3 — Batch & CoA System**
Batch-level inventory tracking, S3-hosted CoA PDFs displayed on product pages, FIFO allocation logic, low stock alerts, expiration tracking, and Filament batch management. The product detail page's placeholder CoA section gets replaced with real `<x-coa.accordion-list>` data.
