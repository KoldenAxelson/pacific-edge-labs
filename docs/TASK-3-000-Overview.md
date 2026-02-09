# [TASK-3-000] Phase 3 Overview: Batch & CoA System

## Purpose
This is a conversational task to plan and generate all Phase 3 tasks with the user. Phase 3 builds the batch-level inventory tracking and Certificate of Analysis (CoA) integration that is Pacific Edge's PRIMARY COMPETITIVE DIFFERENTIATOR. This is the "wow factor" that shows Shane/Eldon you can do what Wix can't.

## Phase 3 Goals
- Design batch database schema and relationships
- Implement batch-level inventory tracking
- Integrate AWS S3 for CoA PDF storage
- Build CoA display on product pages
- Create batch management in Filament admin
- Implement batch selection logic (which batch gets allocated)
- Design "reorder this exact batch" functionality
- Build batch expiration tracking
- Create low-stock alerts per batch

## Key Decisions Already Made

### Why This Matters
**Competitive landscape:**
- Competitors (Peptide Sciences, etc.) have CoAs but require email request
- No competitor shows CoAs directly on product pages
- No competitor does batch-level traceability visible to customers
- This is Pacific Edge's differentiator and trust builder

**Payment processor requirements:**
- Processors look for visible CoAs
- Batch tracking shows professionalism and compliance
- Transparency = less risk of account freeze

**Customer value:**
- Researchers want to know exactly what they're getting
- Batch-to-batch consistency matters
- Being able to reorder the exact same batch is huge
- Purity % visible upfront builds trust

### Database Relationships
```
Products (1) ───< (many) Batches ───< (many) CoAs
                                   └─< (many) OrderItems (Phase 5)
```

**Batch attributes:**
- batch_number (e.g., "PEL-2025-0142")
- product_id (foreign key)
- quantity_available (inventory count)
- quantity_allocated (reserved in carts, Phase 4)
- test_date (when CoA was issued)
- expiration_date (calculate from test_date + shelf life)
- purity_percentage (e.g., 99.3%)
- coa_file_path (S3 URL)
- active (boolean, can disable old batches)
- created_at, updated_at

**CoA file handling:**
- PDFs stored in S3 bucket (private or public-read?)
- Naming convention: `coas/{product_sku}/{batch_number}.pdf`
- Laravel Storage facade for abstraction
- Direct download link vs inline preview?

### Batch Selection Logic
**When customer adds product to cart, which batch gets allocated?**

**Option A: FIFO (First In, First Out)**
- Oldest batch gets used first
- Prevents expiration waste
- Standard inventory practice

**Option B: FEFO (First Expired, First Out)**
- Batch closest to expiration gets used first
- More complex but prevents waste

**Option C: Highest Purity First**
- Customer always gets best quality
- Might leave lower purity batches unsold

**Option D: Customer chooses batch**
- Show available batches, let customer pick
- More transparency, slower checkout

**Recommended:** Start with FIFO (Option A), add customer choice later if needed.

### S3 Integration
**Bucket structure:**
```
pacific-edge-coas/
  ├─ products/
  │   ├─ {product_sku}/
  │   │   ├─ {batch_number}.pdf
  │   │   └─ ...
  └─ ...
```

**Access control:**
- Public read for CoAs (customers can download)
- Signed URLs if want to track downloads (more complex)
- Start simple: public read

**Upload process:**
- Admin uploads PDF via Filament
- File stored in S3
- S3 path saved to batch.coa_file_path
- Display link on product page

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Batch Schema Details**
   - "Should we track manufacturer/supplier per batch?"
   - "Need batch notes/comments field for internal use?"
   - "How to handle batch quantity? (units? vials? mg?)"
   - "Should quantity_allocated be a separate field or calculated from cart items?"

2. **CoA Display**
   - "Where on product page should CoA appear?"
     - Dedicated "Certificate of Analysis" section
     - Expandable accordion
     - Modal popup
     - Separate tab
   - "Show PDF inline (embed) or just download link?"
   - "Display multiple batches (if product has 2+ active batches) or just current batch?"

3. **Batch Selection Logic**
   - "Confirm FIFO (oldest first) for automatic batch allocation?"
   - "Should customers see which batch they're getting BEFORE adding to cart?"
   - "What happens when batch runs out mid-checkout? (reallocate to next batch?)"

4. **Batch Lifecycle**
   - "When should batch be marked inactive? (expired? sold out?)"
   - "Archive old batches or soft delete?"
   - "Need batch history/audit log?"

5. **Filament Admin Interface**
   - "When creating new batch, require CoA upload or allow adding later?"
   - "Bulk import batches from CSV/Excel?"
   - "Low stock threshold per batch? (alert when quantity < X)"

6. **Reorder Functionality**
   - "Should 'reorder exact batch' be prominent on order history page?"
   - "What if batch is sold out? (show 'similar batch available' message?)"
   - "Track which customers prefer which batches? (for future restocking)"

7. **S3 Configuration**
   - "Confirm bucket name: `pacific-edge-coas`?"
   - "Region: us-west-2 (same as Lightsail for low latency)?"
   - "Public read access or signed URLs?"
   - "CDN (CloudFront) for faster PDF delivery or overkill for demo?"

## Suggested Phase 3 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-3-001:** Database Schema for Batches Table
- **TASK-3-002:** Create Batch Migration
- **TASK-3-003:** Build Batch Model with Product Relationship
- **TASK-3-004:** S3 Bucket Setup & Laravel Storage Configuration
- **TASK-3-005:** Filament Batch Resource (CRUD + CoA upload)
- **TASK-3-006:** CoA File Upload Handler (S3 integration)
- **TASK-3-007:** Batch Selection Logic (FIFO service class)
- **TASK-3-008:** Product Detail Page - Add CoA Section
- **TASK-3-009:** Batch Info Display Component (batch #, purity %, test date)
- **TASK-3-010:** CoA Download/Preview Component
- **TASK-3-011:** Batch Inventory Tracking (quantity updates)
- **TASK-3-012:** Low Stock Alerts (admin notifications)
- **TASK-3-013:** Batch Expiration Logic (auto-deactivate expired batches)
- **TASK-3-014:** Batch Seeder (create sample batches for demo products)

## AI Prompt Template
```
I'm starting Phase 3 of Pacific Edge Labs - Batch & CoA System.

This is THE competitive differentiator. No competitor shows CoAs on product pages. No competitor does visible batch traceability.

Phase 3 goals:
1. Build batch-level inventory tracking (Product → Batches → CoAs)
2. Integrate S3 for CoA PDF storage
3. Show CoAs directly on product pages
4. Create batch management in Filament
5. Implement batch selection logic (FIFO by default)
6. Build batch expiration and low-stock tracking

Context:
- Products already exist from Phase 2
- Each product can have multiple active batches
- Each batch has: batch_number, quantity, test_date, purity_%, CoA PDF
- Customers see which batch they're getting (transparency builds trust)
- Payment processors want to see CoAs visible (compliance requirement)

S3 bucket will be: `pacific-edge-coas`
CoA path: `products/{product_sku}/{batch_number}.pdf`

Let's start by finalizing the batch schema. Should we track manufacturer/supplier per batch?
```

## Important Reminders

### For Database Design:
- Batch number should be unique (add unique index)
- Consider UUID for batch IDs (more secure than auto-increment)
- Track both quantity_available and quantity_allocated (cart reserves)
- Expiration date calculated or manually set? (shelf life varies by peptide)
- Soft deletes for batch (keep history even when "deleted")

### For S3 Integration:
- Use Laravel Storage facade (swap S3 for local in dev if needed)
- Store relative paths in database, not full S3 URLs (portability)
- Test file upload/download in local dev before pushing to S3
- Consider file size limits (CoAs are usually <5MB)
- Validate PDF format on upload (prevent malicious files)

### For Batch Selection:
- Create service class for batch allocation logic (don't put in model)
- Handle edge cases: all batches sold out, batch sold out during checkout
- Consider locking mechanism for high-traffic (prevent overselling)
- Log batch allocation decisions (for debugging/audit)

### For CoA Display:
- Mobile-friendly (PDF viewing on mobile can be tricky)
- Fast load times (lazy load PDF preview if embedded)
- Clear download button (not everyone wants inline preview)
- Show batch metadata prominently (purity %, test date, batch #)
- Use Phase 1 badge components for purity % display

### For Admin Interface:
- Filament relationship manager: Product hasMany Batches
- Validate CoA upload (PDF only, file size check)
- Show inventory status clearly (quantity available, allocated, sold)
- Bulk actions: deactivate expired batches, download all CoAs, etc.
- Search/filter batches by product, status, expiration date

### For Expiration Tracking:
- Cron job to check for expired batches daily
- Auto-deactivate expired batches (don't show to customers)
- Admin notification when batches are expiring soon (7 days warning?)
- Grace period before auto-deactivation (some peptides stable past date)

## Success Criteria for Phase 3

At the end of Phase 3, you should have:
- [ ] Batches table migrated with all necessary fields
- [ ] Batch model with Product relationship
- [ ] S3 bucket created and configured in Laravel
- [ ] Filament Batch resource for managing batches
- [ ] CoA file upload working (local dev + S3 production)
- [ ] Product detail page shows batch info and CoA
- [ ] CoA downloadable/viewable from product page
- [ ] Batch selection logic implemented (FIFO)
- [ ] Inventory tracking working (quantity decrements)
- [ ] Low stock alerts configured
- [ ] Batch expiration tracking working
- [ ] Seeders create sample batches with mock CoAs
- [ ] Admin can see which batch is allocated to which order (Phase 5 integration point)

**Cart/checkout not yet built** - that's Phase 4. But batch allocation logic should be ready to integrate.

---
**Next Phase:** TASK-4-000 (Cart, Checkout & Compliance)  
**Previous Phase:** TASK-2-000 (Product Catalog & Pages)  
**Phase:** 3 (Batch & CoA System)  
**Approach:** Conversational - finalize batch schema and S3 setup, then build  
**Estimated Duration:** 2-3 days of focused work  
**Priority:** CRITICAL - this is the competitive differentiator and demo wow factor
