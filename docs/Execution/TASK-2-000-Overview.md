# [TASK-2-000] Phase 2 Overview: Product Catalog & Pages

## Purpose
This is a conversational task to plan and generate all Phase 2 tasks with the user. Phase 2 focuses on building the product catalog structure, database models, and public-facing product pages. This establishes the core e-commerce data layer that all other features depend on.

## Phase 2 Goals
- Design database schema for products and categories
- Create migrations for products, categories, and relationships
- Build Product and Category models with relationships
- Create seeders with realistic peptide product data
- Build product listing page (category view)
- Build product detail page
- Implement basic search/filter functionality
- Create admin Filament resources for products (basic CRUD)

## Key Decisions Already Made

### Product Structure
**Current Pacific Edge catalog:**
- ~30 SKUs currently
- Could grow to 60-80 over time (not hundreds)
- Products grouped by category:
  - GLP-1 Agonists (Semaglutide, Tirzepatide, Retatrutide)
  - Recovery & Healing (BPC-157, TB-500, Thymosin)
  - Performance & Growth (Ipamorelin, CJC-1295)
  - Niche research compounds

**Product Attributes:**
- Name (e.g., "Semaglutide 15mg")
- SKU (e.g., "PEL-SEM-15")
- Description (scientific, research-focused language)
- Price (e.g., $74.99)
- Category (belongs to one category)
- Images (multiple product images)
- Active/inactive status
- Research-only disclaimer (per product or global)
- Related research links (PubMed papers, studies)

**NOT in this phase:**
- Batches (that's Phase 3)
- Cart functionality (that's Phase 4)
- Orders (that's Phase 5)

### Database Design Considerations
- Products table: id, sku, name, description, price, category_id, active, created_at, updated_at
- Categories table: id, name, slug, description, sort_order, active, created_at, updated_at
- Product images: separate table or JSON column?
- Research links: separate table or JSON column?
- SEO fields: meta_title, meta_description, keywords?

### Search/Filter Strategy
**For 30-80 products, simple is sufficient:**
- PostgreSQL `ILIKE` for keyword search
- Category filtering via dropdown or sidebar
- No need for Laravel Scout/Meilisearch yet (can add later)

### Navigation Approach
```
Homepage
 ├─ GLP-1 Agonists (category page)
 ├─ Recovery & Healing (category page)
 ├─ Performance & Growth (category page)
 └─ All Products (full catalog, filterable)
```

Each category page shows products in that category.

### Component Reuse from Phase 1
- Product card component (already built)
- Layout containers (already built)
- Badges (already built)
- Buttons (already built)

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Database Schema Details**
   - "Should product images be in separate table or JSON column?"
   - "How many images per product? (main + gallery)"
   - "Research links: separate table or JSON column?"
   - "Need variant system (e.g., 5mg, 10mg, 15mg as separate products or variants)?"

2. **Category Structure**
   - "Is category hierarchy flat or nested? (probably flat for 3-4 categories)"
   - "Do categories need their own landing pages with custom content?"
   - "Category images/icons needed?"

3. **Product Page Content**
   - "What sections on product detail page?"
     - Product image(s)
     - Name, SKU, price
     - Description
     - Research links
     - Disclaimer (from Phase 1 component)
     - "Add to cart" button (non-functional until Phase 4)
     - CoA section (placeholder for Phase 3)
   - "Tabs or single-page scroll?"

4. **Search/Filter Implementation**
   - "Just keyword search or also filter by price range, category, etc.?"
   - "Search bar in header or dedicated search page?"
   - "Real-time search results (AJAX) or full-page refresh?"

5. **Admin Filament Resource**
   - "Build Filament resource for products now or defer to Phase 6?"
   - "If now, what fields are editable in admin?"
   - "Need bulk actions (activate/deactivate multiple products)?"

6. **SEO Considerations**
   - "Custom meta tags per product or auto-generate from description?"
   - "Structured data (schema.org) for products?"
   - "Canonical URLs?"

7. **Data Seeding**
   - "How many products in seed data? (10-15 to demo variety?)"
   - "Use real Pacific Edge products or generic placeholders?"
   - "Real prices or placeholder pricing?"

## Suggested Phase 2 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-2-001:** Database Schema Design (products, categories, images, research_links)
- **TASK-2-002:** Create Migrations (products, categories, pivot tables if needed)
- **TASK-2-003:** Build Product Model with Relationships
- **TASK-2-004:** Build Category Model with Relationships
- **TASK-2-005:** Create Product Seeder with Realistic Data
- **TASK-2-006:** Create Category Seeder
- **TASK-2-007:** Build Category Listing Page (shows products in category)
- **TASK-2-008:** Build Product Detail Page
- **TASK-2-009:** Implement Search Functionality (basic keyword search)
- **TASK-2-010:** Build "All Products" Page with Filters
- **TASK-2-011:** Filament Product Resource (basic CRUD)
- **TASK-2-012:** Filament Category Resource (basic CRUD)
- **TASK-2-013:** SEO Meta Tags & Structured Data

## AI Prompt Template
```
I'm starting Phase 2 of Pacific Edge Labs - Product Catalog & Pages.

Phase 2 goals:
1. Design database schema for products and categories
2. Create migrations and models
3. Seed realistic peptide product data
4. Build product listing and detail pages
5. Implement basic search/filter
6. Create Filament admin resources for product management

Context:
- ~30 products currently, could grow to 60-80
- Products grouped into 3-4 categories (GLP-1 Agonists, Recovery, Performance)
- Each product: name, SKU, description, price, images, research links
- Batches/CoAs come in Phase 3 (not this phase)
- Cart/checkout in Phase 4 (not this phase)

Design components from Phase 1 are ready to use:
- Product card component
- Buttons
- Badges
- Layout containers

Let's start by finalizing the database schema. Should product images be in a separate table or JSON column?
```

## Important Reminders

### For Database Design:
- Use soft deletes for products (keep data even when "deleted")
- Index commonly queried fields (category_id, sku, active)
- Consider full-text search indexes for name/description (PostgreSQL)
- Foreign keys for referential integrity (category_id → categories.id)

### For Models:
- Define relationships clearly (Category hasMany Products, Product belongsTo Category)
- Use accessors/mutators for formatting (price in cents, display with $)
- Scope for active products (whereActive(true))
- Cast JSON columns appropriately

### For Seeders:
- Use realistic data (actual peptide names, real pricing)
- Vary product attributes (some expensive, some cheap, different categories)
- Include both active and inactive products (test filtering)
- Add enough products to show pagination/filtering (10-15 minimum)

### For Product Pages:
- Mobile-responsive (many users browse on mobile despite desktop assumption)
- Fast page loads (optimize images, lazy loading)
- Clear CTA ("Add to Cart" even if non-functional yet)
- Research-only language throughout (never imply human use)
- Disclaimers visible (use Phase 1 disclaimer component)

### For Search/Filter:
- Keep it simple for demo (just keyword + category filter)
- Can enhance post-demo if needed
- Make sure search works on product name AND description
- Debounce search input to avoid excessive queries (if AJAX)

### For SEO:
- Clean URLs (e.g., `/products/semaglutide-15mg` not `/products/123`)
- Descriptive meta titles and descriptions
- Schema.org Product markup (price, availability, brand)
- Alt text for all images
- Semantic HTML (h1, h2, article tags)

## Success Criteria for Phase 2

At the end of Phase 2, you should have:
- [ ] Products table migrated with all necessary fields
- [ ] Categories table migrated
- [ ] Product and Category models with relationships
- [ ] Seeders create 10-15 realistic peptide products
- [ ] Category listing pages showing products
- [ ] Product detail pages with all content sections
- [ ] Basic search functionality working
- [ ] "All Products" page with category filter
- [ ] Filament resources for managing products/categories (if done in Phase 2)
- [ ] SEO meta tags on product pages
- [ ] Responsive design on mobile/tablet
- [ ] Fast page loads (<2 seconds)
- [ ] Research-only disclaimers visible on product pages

**Batches and CoAs not implemented yet** - that's Phase 3. Product pages should have placeholder sections for batch/CoA content.

---
**Next Phase:** TASK-3-000 (Batch & CoA System)  
**Previous Phase:** TASK-1-000 (Design System & Brand Foundation)  
**Phase:** 2 (Product Catalog & Pages)  
**Approach:** Conversational - finalize schema, then generate build tasks  
**Estimated Duration:** 2 days of focused work  
**Priority:** High - foundational data layer for entire platform
