# [TASK-2-002] Create Migrations

## Overview
Create the four new migration files defined in TASK-2-001. Run them, confirm the
schema, then verify foreign key constraints hold.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 1–2 hrs
**Depends on:** TASK-2-001
**Blocks:** TASK-2-003

---

## Files to Create

```
database/migrations/
├── 2026_XX_XX_000001_create_categories_table.php
├── 2026_XX_XX_000002_create_products_table.php
├── 2026_XX_XX_000003_create_product_images_table.php
└── 2026_XX_XX_000004_create_product_research_links_table.php
```

Run in order — products depends on categories existing.

---

## Migration 1: `create_categories_table`

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parent_id')
          ->nullable()
          ->constrained('categories')
          ->nullOnDelete();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->string('hero_title', 200)->nullable();
    $table->smallInteger('sort_order')->default(0);
    $table->boolean('active')->default(true);
    $table->timestamps();

    $table->index('slug');
    $table->index('active');
    $table->index('sort_order');
    $table->index('parent_id');
});
```

---

## Migration 2: `create_products_table`

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
    $table->string('sku', 30)->unique();
    $table->string('name', 200);
    $table->string('slug', 200)->unique();
    $table->string('short_description', 500)->nullable();
    $table->text('description');
    $table->string('form', 100)->nullable();
    $table->string('concentration', 100)->nullable();
    $table->string('storage_conditions', 300)->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('compare_price', 10, 2)->nullable();
    $table->string('meta_title', 200)->nullable();
    $table->text('meta_description')->nullable();
    $table->boolean('featured')->default(false);
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index('category_id');
    $table->index('slug');
    $table->index('active');
    $table->index('featured');
    $table->index(['active', 'deleted_at']);  // common combined query
});
```

**Add full-text index after table creation (PostgreSQL):**
```php
DB::statement("CREATE INDEX products_fulltext_idx ON products USING GIN (to_tsvector('english', name || ' ' || description))");
```
Wrap in `if (DB::getDriverName() === 'pgsql')` so SQLite tests don't break.

---

## Migration 3: `create_product_images_table`

```php
Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')
          ->constrained('products')
          ->cascadeOnDelete();
    $table->string('path', 500);
    $table->string('alt_text', 200)->nullable();
    $table->smallInteger('sort_order')->default(0);
    $table->timestamps();

    $table->index('product_id');
    $table->index(['product_id', 'sort_order']);
});
```

---

## Migration 4: `create_product_research_links_table`

```php
Schema::create('product_research_links', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')
          ->constrained('products')
          ->cascadeOnDelete();
    $table->string('title', 300);
    $table->string('authors', 500)->nullable();
    $table->smallInteger('publication_year')->nullable();
    $table->string('journal', 200)->nullable();
    $table->string('pubmed_id', 20)->nullable();
    $table->string('url', 500);
    $table->smallInteger('sort_order')->default(0);
    $table->timestamps();

    $table->index('product_id');
    $table->index(['product_id', 'sort_order']);
});
```

---

## Steps

1. Generate migration stubs:
```bash
php artisan make:migration create_categories_table
php artisan make:migration create_products_table
php artisan make:migration create_product_images_table
php artisan make:migration create_product_research_links_table
```

2. Fill in each migration from the definitions above.

3. Run migrations:
```bash
php artisan migrate
```

4. Verify in psql or Tinker:
```php
Schema::getColumnListing('products');
Schema::getColumnListing('categories');
```

5. Confirm foreign key: try inserting a product with a non-existent `category_id` —
   it should throw an integrity constraint violation.

---

## Rollback Safety

All four tables can be safely rolled back in reverse order (research_links → images → products → categories). The `restrictOnDelete()` on `category_id` means you cannot delete a category that has products — intentional.

---

## Acceptance Criteria
- [ ] `php artisan migrate` runs without errors
- [ ] All four tables exist with correct columns and types
- [ ] Foreign key constraints are enforced
- [ ] Full-text GIN index exists on `products` (PostgreSQL only)
- [ ] `php artisan migrate:rollback` works cleanly
- [ ] SQLite (test environment) does not throw errors on migration
