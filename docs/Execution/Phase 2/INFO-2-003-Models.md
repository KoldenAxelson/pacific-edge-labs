# [INFO-2-003] Product & Category Models - Completion Report

## Metadata
- **Task:** TASK-2-003-Models
- **Phase:** 2 (Product Catalog & Pages)
- **Completed:** 2026-02-16
- **Duration:** 35m
- **Status:** ✅ Complete

## What We Did
Created all four Eloquent models and both factories. Hit three issues during smoke testing, all resolved.

- Created `Category` model with parent/children self-referential relationships, `active`/`topLevel`/`ordered` scopes, and `url`/`activeProductCount` accessors
- Created `Product` model with SoftDeletes, category/images/researchLinks relationships, `active`/`featured`/`inCategory`/`search` scopes, and price/meta/sale accessors
- Created `ProductImage` model with `url` accessor (Storage::url) and `isPrimary` accessor
- Created `ProductResearchLink` model with `pubmedUrl` and `citation` accessors
- Created `CategoryFactory` with the 6 real category names
- Created `ProductFactory` with `onSale()` and `inactive()` states

## Deviations from Plan

- **Missing `HasFactory` trait:** Both `Category` and `Product` models were initially created without `use HasFactory`, causing `Call to undefined method factory()`. Added the trait and import to both models.
- **`$this->faker` null in factories:** Factories initially used `$this->faker->` (spec pattern) but Faker's `Generator` class wasn't bound in the container on Lightsail (dev dependencies not installed). Switched both factories to use Laravel 12's `fake()` helper instead.
- **`whereILike` doesn't exist:** The spec used `whereILike()` / `orWhereILike()` in the `search` scope, but this method doesn't exist in Laravel 12. Replaced with `whereRaw('column ILIKE ?', [$term])` for PostgreSQL-native case-insensitive search.

## Confirmed Working (Tinker smoke test on Lightsail)
- ✅ `Category::create(...)` — inserts correctly
- ✅ `Product::create(...)` — inserts with FK to category
- ✅ `$product->category->name` — relationship resolves ("Test Category")
- ✅ `$product->formatted_price` — returns "$74.99"
- ✅ `$product->effective_meta_title` — falls back to "Test Semaglutide 15mg | Pacific Edge Labs"
- ✅ `$product->is_on_sale` — returns false (no compare_price)
- ✅ `Product::active()->count()` — returns 1
- ✅ `Category::active()->ordered()->get()->pluck('name')` — returns collection
- ✅ `ProductResearchLink::create(...)` — inserts with FK to product
- ✅ `$link->pubmed_url` — returns "https://pubmed.ncbi.nlm.nih.gov/33567185"
- ✅ `$link->citation` — returns "Smith J, et al. (2021)"
- ✅ `Product::search('sema')->count()` — returns 1 (ILIKE working)
- ✅ Cleanup: `forceDelete()` and `delete()` all passed
- ⏳ Factories not tested (Faker not installed on Lightsail — dev dependency)
- ⏳ `$image->url` not tested (no image records yet, needs S3 config)
- ⏳ `$product->url` / `$category->url` not tested (routes don't exist until TASK-2-006/2-007)

## Important Notes
- The `url` accessor on Category and Product depends on named routes (`categories.show`, `products.show`) — will throw until TASK-2-006/2-007
- `Product::search()` uses raw `ILIKE` (PostgreSQL-specific). If SQLite is ever used for tests, this scope will need a driver check or a `LIKE LOWER()` fallback.
- `ProductImage::url` uses `Storage::url()` — requires S3 disk configuration in `config/filesystems.php` for production
- Faker/factories are dev-only on Lightsail. Seeders (TASK-2-004/2-005) use manual data, not factories, so this is fine.

## Blockers Encountered
- **Blocker:** Missing `HasFactory` trait → **Resolution:** Added `use HasFactory` to Category and Product models
- **Blocker:** `$this->faker` null on Lightsail → **Resolution:** Switched factories to `fake()` helper
- **Blocker:** `whereILike` undefined in Laravel 12 → **Resolution:** Replaced with `whereRaw('ILIKE')`

## Configuration Changes
```
No configuration changes — only new model and factory files
```

## Next Steps
- Proceed to TASK-2-004: Category Seeder

## Files Created/Modified
- `app/Models/Category.php` - created (then patched: added HasFactory)
- `app/Models/Product.php` - created (then patched: added HasFactory, fixed search scope)
- `app/Models/ProductImage.php` - created
- `app/Models/ProductResearchLink.php` - created
- `database/factories/CategoryFactory.php` - created (then patched: $this->faker → fake())
- `database/factories/ProductFactory.php` - created (then patched: $this->faker → fake())
- `docs/Execution/Phase 2/INFO-2-003-Models.md` - created - this completion report

---
**For Next Claude:** Three spec errors were caught and fixed: (1) missing `HasFactory` trait on models, (2) `$this->faker` doesn't resolve on Lightsail so use `fake()`, (3) `whereILike` doesn't exist in Laravel 12 so use `whereRaw('ILIKE')`. The `url` accessors will throw until routes exist in TASK-2-006/2-007.
