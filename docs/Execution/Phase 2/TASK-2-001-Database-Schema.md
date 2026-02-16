# [TASK-2-001] Database Schema Design

## Overview
Define the complete database schema for Phase 2. All decisions have been finalized
in the TASK-2-000 conversation. This task is a reference document — read it before
writing any migrations.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 1 hr (design review, no code)
**Depends on:** Nothing — this is the foundation
**Blocks:** TASK-2-002

---

## Locked Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Product images | Separate `product_images` table | Per-image alt text, sort order, S3 path management |
| Research links | Separate `product_research_links` table | Full citation metadata (PubMed ID, authors, year) |
| Variants | No variant table — each SKU is its own product | Confirmed against live catalog |
| Soft deletes | Yes on `products` | Products deactivated, never hard-deleted |
| Category nesting | `parent_id` nullable on categories | Zero cost hedge for future sub-categories |
| SEO meta | Custom fields on products, nullable, auto-generated when null | |

---

## Table Definitions

### `categories`
```sql
id              bigint          PRIMARY KEY AUTO-INCREMENT
parent_id       bigint NULLABLE FK → categories.id   -- for future sub-categories
name            varchar(100)    NOT NULL
slug            varchar(100)    NOT NULL UNIQUE
description     text            NULLABLE              -- category landing page intro
hero_title      varchar(200)    NULLABLE              -- custom H1 for category page
sort_order      smallint        NOT NULL DEFAULT 0
active          boolean         NOT NULL DEFAULT true
created_at      timestamp
updated_at      timestamp
```
**Indexes:** `slug`, `active`, `sort_order`, `parent_id`

---

### `products`
```sql
id                  bigint          PRIMARY KEY AUTO-INCREMENT
category_id         bigint          NOT NULL FK → categories.id
sku                 varchar(30)     NOT NULL UNIQUE
name                varchar(200)    NOT NULL
slug                varchar(200)    NOT NULL UNIQUE
short_description   varchar(500)    NULLABLE  -- card hover / research summary (1–2 sentences)
description         text            NOT NULL  -- full product page description
form                varchar(100)    NULLABLE  -- e.g. "Lyophilized powder"
concentration       varchar(100)    NULLABLE  -- e.g. "15mg per vial"
storage_conditions  varchar(300)    NULLABLE  -- e.g. "Store in cool, dry environment..."
price               decimal(10,2)   NOT NULL
compare_price       decimal(10,2)   NULLABLE  -- original price if on sale
meta_title          varchar(200)    NULLABLE  -- null = auto-generated from name
meta_description    text            NULLABLE  -- null = auto-generated from short_description
featured            boolean         NOT NULL DEFAULT false
active              boolean         NOT NULL DEFAULT true
created_at          timestamp
updated_at          timestamp
deleted_at          timestamp       NULLABLE  -- soft deletes
```
**Indexes:** `category_id`, `sku`, `slug`, `active`, `featured`
**Full-text:** GIN index on `name` + `description` (PostgreSQL `to_tsvector`)

---

### `product_images`
```sql
id              bigint          PRIMARY KEY AUTO-INCREMENT
product_id      bigint          NOT NULL FK → products.id (cascade delete)
path            varchar(500)    NOT NULL   -- S3 key, e.g. "products/semaglutide/primary.png"
alt_text        varchar(200)    NULLABLE
sort_order      smallint        NOT NULL DEFAULT 0
created_at      timestamp
updated_at      timestamp
```
**Indexes:** `product_id`, `sort_order`
**Note:** Image at `sort_order = 0` is the primary/hero image.

---

### `product_research_links`
```sql
id                  bigint          PRIMARY KEY AUTO-INCREMENT
product_id          bigint          NOT NULL FK → products.id (cascade delete)
title               varchar(300)    NOT NULL
authors             varchar(500)    NULLABLE  -- "Smith J, Jones A, et al."
publication_year    smallint        NULLABLE
journal             varchar(200)    NULLABLE
pubmed_id           varchar(20)     NULLABLE  -- PMID for linking to pubmed.ncbi.nlm.nih.gov
url                 varchar(500)    NOT NULL
sort_order          smallint        NOT NULL DEFAULT 0
created_at          timestamp
updated_at          timestamp
```
**Indexes:** `product_id`, `sort_order`

---

## Relationships Summary

```
categories
  └── hasMany: products (via category_id)
  └── hasMany: children (via parent_id — self-referential, currently unused)

products
  ├── belongsTo: category
  ├── hasMany: product_images    (cascade on delete)
  └── hasMany: product_research_links (cascade on delete)
```

---

## Category Seed Data

Six flat categories. `parent_id` is null for all current entries.

| sort_order | name | slug |
|---|---|---|
| 1 | GLP-1 & Metabolic | `glp-1-metabolic` |
| 2 | Recovery & Healing | `recovery-healing` |
| 3 | Performance & Growth | `performance-growth` |
| 4 | Cognitive & Longevity | `cognitive-longevity` |
| 5 | Sexual Health | `sexual-health` |
| 6 | Ancillaries | `ancillaries` |

---

## Product → Category Mapping (all 30 SKUs)

| Category | Products |
|---|---|
| GLP-1 & Metabolic | Semaglutide 15mg, Tirzepatide, Retatrutide, Cagrilintide 10mg, AOD-9604 5mg, Adipotide (FTPP) 5mg |
| Recovery & Healing | TB-500 10mg, Wolverine Stack, GHK-CU, LL-37 5mg, Thymosin Alpha-1 5mg |
| Performance & Growth | IGF-1LR3 1mg, CJC-1295+Ipamorelin, Sermorelin 5mg, Gonadorelin 5mg, HCG 5000IU, SLU-PP-332 5mg, KLOW 80mg |
| Cognitive & Longevity | Semax 5mg, Selank 10mg, DSIP 10mg, NAD+, Oxytocin Acetate 10mg, Kisspeptin-10 10mg, Glutathione 1500mg, Epithalon 50mg |
| Sexual Health | PT-141 10mg, Melanotan 2 10mg, Melanotan 1 10mg |
| Ancillaries | Hospira BAC Water 30ml |

---

## Acceptance Criteria
- [ ] Schema reviewed and understood before TASK-2-002 begins
- [ ] All table definitions match the decisions above
- [ ] No changes to existing tables (users, payment_transactions, etc.)

---

## Notes
- Batches and CoA tables come in Phase 3 — do NOT add `batch_id` or CoA fields here
- Cart/order tables come in Phase 4 — do NOT add `cart_items` here
- The `short_description` field maps to the `researchSummary` prop on `<x-product.card>`
- `compare_price` enables the "Sale Price" display seen on BAC Water (current site shows regular + sale price)
