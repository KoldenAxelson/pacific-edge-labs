# Documentation Guide - Pacific Edge Labs

> **Standards for maintaining living documentation that evolves with the project**

This guide establishes documentation practices for the Pacific Edge Labs e-commerce platform. The goal is to create clear, maintainable documentation that serves both current development and future handoff to Pacific Edge Labs.

## 🎯 Documentation Philosophy

### Principles

1. **Document Why, Not Just What**
   - Code shows WHAT it does
   - Comments explain WHY it does it that way

2. **Living Documentation**
   - Documentation updates with code changes
   - Outdated docs are worse than no docs

3. **Audience-Aware**
   - Different audiences need different documentation
   - Code comments for developers
   - README for stakeholders
   - Task reports for project tracking

4. **Just-In-Time**
   - Document as you build, not after
   - Don't over-document before understanding the problem

## 📁 Documentation Structure

```
pacific-edge-labs/
├── README.md                              # Project overview, setup, tech stack
├── docs/
│   ├── architecture/                      # Architecture and design decisions
│   │   ├── email-architecture.md          # Email abstraction layer design
│   │   └── payment-architecture.md        # Payment gateway abstraction design
│   ├── Execution/                         # Phase execution tracking
│   │   ├── Phase 0/                       # TASK-* instructions and INFO-* completion reports
│   │   ├── Phase 1/                       # (created per phase)
│   │   └── TASK-X-000-Overview.md         # Phase overview files
│   ├── Guides and Templates/             # Development standards
│   │   ├── Coding_Conventions.md          # Coding standards and conventions
│   │   ├── Documentation_Guide.md         # This file
│   │   └── TEMPLATE-Task-Completion.md    # Template for INFO completion reports
│   ├── history/                           # Project history and milestones
│   │   ├── pacific-edge-project-brief.md  # Original project brief
│   │   └── Phase-0-Completion.md          # Phase 0 completion summary
│   └── reference/                         # Operational reference guides
│       ├── seeding.md                     # Database seeding guide
│       └── testing.md                     # Testing guide
```

## 💬 Inline Comments

### When to Comment

**✅ DO comment:**
- Complex business logic
- Non-obvious solutions
- "Why" behind a decision
- Workarounds or temporary solutions
- Security-sensitive code
- Performance optimizations
- Regulatory/compliance requirements

**❌ DON'T comment:**
- Obvious code
- What the code does (code should be self-documenting)
- Redundant information
- Outdated information (delete or update)

### Comment Examples

```php
// ✅ GOOD - Explains WHY
// Use FIFO allocation to prevent batch expiration waste
// Newer batches have longer shelf life
$batch = $this->allocateOldestBatch($product);

// ✅ GOOD - Explains complex logic
// Payment processors require attestation confirmation within 30 days
// of order placement for compliance audits. Store both timestamp
// and IP address per legal team requirement (2025-01-15 meeting)
$this->complianceLogger->logAttestation(
    $order,
    'researcher_confirmation',
    $request->ip()
);

// ✅ GOOD - Warns about edge case
// Note: ShipStation API sometimes returns null for tracking numbers
// on newly created shipments. Retry once after 30 seconds if null.
if (!$trackingNumber) {
    sleep(30);
    $trackingNumber = $this->shipStation->getTracking($orderId);
}

// ❌ BAD - Obvious
// Get the user
$user = auth()->user();

// ❌ BAD - States what code does
// Loop through products and calculate total
foreach ($products as $product) {
    $total += $product->price;
}
```

### TODO Comments

Use TODO for future improvements, but link to tracked issues when possible.

```php
// TODO: Implement batch reallocation when preferred batch sells out
// See GitHub Issue #42

// FIXME: This causes N+1 queries with large order volumes
// Needs eager loading optimization before production launch

// HACK: Temporary workaround for Authorize.Net API bug
// Remove when they fix CVV validation endpoint (reported 2025-02-01)
```

## 📚 DocBlocks

### Class DocBlocks

Every class should have a DocBlock explaining its purpose.

```php
<?php

namespace App\Services;

/**
 * Handles batch-level inventory allocation for orders.
 * 
 * Uses First-In-First-Out (FIFO) strategy to prevent batch expiration
 * waste. Automatically falls back to next available batch if primary
 * allocation fails.
 *
 * @see App\Models\Batch
 * @see App\Models\Product
 */
class BatchAllocationService
{
    // ...
}
```

### Method DocBlocks

Document public methods, especially in services and complex logic.

```php
/**
 * Allocate inventory from the oldest available batch for a product.
 *
 * This method implements FIFO (First-In-First-Out) inventory allocation
 * to prevent batch expiration. If the oldest batch has insufficient
 * quantity, it will partially allocate from that batch and recursively
 * allocate the remainder from the next oldest batch.
 *
 * @param Product $product The product to allocate inventory for
 * @param int $quantity The quantity to allocate
 * @return array Array of batch allocations: [['batch_id' => int, 'quantity' => int], ...]
 * @throws InsufficientInventoryException When total available inventory < requested quantity
 */
public function allocateFromOldestBatch(Product $product, int $quantity): array
{
    // Implementation...
}
```

### Parameter Documentation

```php
/**
 * Process a payment and create order record.
 *
 * @param User $user The authenticated user placing the order
 * @param array $orderData Order details including:
 *                         - items: array of ['product_id', 'batch_id', 'quantity', 'price']
 *                         - total: decimal order total
 *                         - shipping: array of address details
 *                         - attestations: array of compliance confirmations
 * @param string $paymentToken Gateway-specific payment token
 * @return Order The created order instance
 * @throws PaymentFailedException When payment processing fails
 * @throws InsufficientInventoryException When product is out of stock
 */
public function processOrderPayment(User $user, array $orderData, string $paymentToken): Order
{
    // Implementation...
}
```

### Property DocBlocks

Document non-obvious properties.

```php
class Order extends Model
{
    /**
     * The number of days after which unpaid orders are automatically cancelled.
     *
     * This prevents inventory reservation indefinitely while allowing
     * reasonable time for payment processing issues.
     */
    const PAYMENT_TIMEOUT_DAYS = 3;

    /**
     * Compliance attestations confirmed by user at checkout.
     * 
     * Stored as JSON array with keys:
     * - age_verified: bool
     * - researcher_confirmation: bool
     * - research_only_confirmation: bool
     * - ip_address: string
     * - confirmed_at: timestamp
     *
     * @var array
     */
    protected $casts = [
        'attestations' => 'array',
    ];
}
```

## 🗄 Database Documentation

### Migration Comments

Add comments to clarify complex fields or constraints.

```php
public function up(): void
{
    Schema::create('compliance_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
        
        // Checkpoint types: age_verification, researcher_attestation, research_only_attestation
        $table->string('checkpoint_type', 50);
        
        // Store IP address for audit trail (payment processor requirement)
        $table->ipAddress('ip_address');
        
        // User agent string for device/browser tracking
        $table->text('user_agent');
        
        $table->timestamp('confirmed_at');
        $table->timestamps();
        
        // Compliance reports query by order_id and checkpoint_type
        $table->index(['order_id', 'checkpoint_type']);
    });
}
```

### Schema Documentation

Maintain `docs/architecture/database-schema.md`:

```markdown
# Database Schema

## Overview
This document describes the database structure for Pacific Edge Labs e-commerce platform.

## Core Tables

### products
Stores product catalog information.

**Key Relationships:**
- `belongs_to` Category
- `has_many` Batches
- `has_many` CartItems
- `has_many` OrderItems

**Important Fields:**
- `sku`: Unique product identifier (format: PEL-XXX-##)
- `price`: Stored as decimal(10,2) in USD
- `active`: Boolean flag for product visibility

**Business Rules:**
- Products can be deactivated but never deleted (preserve order history)
- SKU must be unique across all products
- Price changes don't affect existing orders (order_items stores price at time of purchase)

### batches
Batch-level inventory tracking with CoA association.

**Key Relationships:**
- `belongs_to` Product
- `has_many` OrderItems

**Important Fields:**
- `batch_number`: Unique identifier (format: PEL-YYYY-####)
- `quantity_available`: Current stock level
- `quantity_allocated`: Reserved for pending checkouts
- `purity_percentage`: Lab test result (decimal, 2 places)
- `coa_path`: S3 path to CoA PDF

**Business Rules:**
- FIFO allocation: oldest batches allocated first
- Expired batches auto-deactivated nightly (cron job)
- CoA required before batch can be activated
- quantity_available - quantity_allocated = true available inventory
```

## 🏗 Architectural Decision Records (ADRs)

Document significant architectural decisions using ADR format.

**Location:** `docs/architecture/`

**Template (inline):**

```markdown
# [Number]. [Title]

**Date:** YYYY-MM-DD

**Status:** Proposed | Accepted | Deprecated | Superseded

**Deciders:** [Names or roles]

**Context:**
What is the issue we're trying to solve? What constraints exist?

**Decision:**
What solution did we choose?

**Consequences:**
What becomes easier? What becomes harder?

**Alternatives Considered:**
What other options did we evaluate?
```

### Example ADR

**File:** `docs/architecture/batch-level-inventory.md` (example)

```markdown
# 2. Batch-Level Inventory Tracking

**Date:** 2025-02-09

**Status:** Accepted

**Deciders:** Solo Developer, Pacific Edge Labs stakeholders

## Context

Pacific Edge Labs needs to track inventory at a more granular level than typical e-commerce platforms. Each product (e.g., Semaglutide 15mg) comes in multiple batches from suppliers, with each batch having:
- Unique batch number
- Certificate of Analysis (CoA) with purity percentage
- Test date and expiration date
- Separate inventory count

Customers need to see which specific batch they're receiving for:
1. Transparency (competitive advantage)
2. Compliance (payment processor requirement)
3. Quality assurance (if issues arise, can trace to batch)

## Decision

Implement a separate `batches` table with one-to-many relationship to products:
- Products have multiple batches
- Orders reference specific batch_id
- Inventory tracking at batch level (not product level)
- FIFO allocation strategy (oldest batch allocated first)

## Consequences

**Positive:**
- Complete traceability from customer to CoA
- Prevents batch expiration waste through FIFS
- Competitive advantage over competitors
- Satisfies payment processor compliance requirements

**Negative:**
- More complex inventory management
- Admin must manage batches, not just products
- Cart allocation logic more complex (must select batch)
- Cannot simply "restock" a product, must add new batch

## Alternatives Considered

### Alternative 1: Product-Level Inventory Only
Store CoA at product level, track total inventory without batches.

**Rejected because:**
- Cannot track which customer received which batch
- No way to handle multiple CoAs per product
- Loses competitive advantage of transparency

### Alternative 2: SKU Variants (Different SKUs per Batch)
Treat each batch as a separate SKU (e.g., SEM-15-BATCH1, SEM-15-BATCH2).

**Rejected because:**
- Clutters product catalog (30 products × 5 batches = 150 SKUs)
- Confusing for customers
- Harder to implement "reorder same product" functionality
- Product pages would be fragmented
```

## 📋 Task Documentation

### Phase Overview (TASK-X-000-Overview.md)

Conversational planning document for each phase.

**Purpose:**
- Define phase goals
- List key decisions
- Provide conversation starters for AI
- Suggest tasks to generate

**Template:** `TASK-0-000-Overview.md` (see project files)

### Task Documents (TASK-X-00Y-TaskName.md)

Detailed step-by-step instructions for implementation.

**Structure:**
```markdown
# [TASK-X-00Y] Task Name

## Overview
Brief description of what this task accomplishes.

## Prerequisites
- [ ] TASK-X-00Y completed
- [ ] Package installed
- [ ] Service configured

## Goals
Bullet list of specific outcomes.

## Step-by-Step Instructions

### 1. First Major Step
Clear commands or code examples.

### 2. Second Major Step
...

## Validation Checklist
- [ ] Specific item to verify
- [ ] Another item to verify

## Common Issues & Solutions

### Issue: Description
**Solution:** How to fix

## Next Steps
What unlocks after this task completes.
```

### Task Completion Reports (INFO-X-00Y-TaskName.md)

Document what actually happened (vs. what was planned).

**Template:** `TEMPLATE-Task-Completion.md`

```markdown
# [INFO-X-00Y] Task Name - Completion Report

## Metadata
- **Task:** TASK-X-00Y-Task-Name
- **Phase:** X (Phase Name)
- **Completed:** 2025-02-09
- **Duration:** 45m (actual time spent)
- **Status:** ✅ Complete | ⚠️ Complete with Notes | 🔄 Partial

## What We Did
Brief summary of actions taken.

## Deviations from Plan
- **Deviation 1:** What changed and why

## Confirmed Working
- ✅ Item with specific verification

## Important Notes
- **Note:** Critical information for future reference

## Blockers Encountered
- **Blocker:** Issue → **Resolution:** How fixed

## Configuration Changes
```
File: path/to/file
Changes: what was modified
```

## Next Steps
Immediate next actions.
```

**Best Practices:**
- Create immediately after completing task
- Be honest about deviations (helps future iterations)
- Include exact commands/configs that worked
- Note any gotchas or surprises

## 📖 README.md Maintenance

### When to Update README

Update `README.md` when:
- Completing a major phase
- Adding new technology to stack
- Changing deployment process
- Adding new npm/composer dependencies
- Project structure changes significantly

### README Sections to Maintain

```markdown
# Pacific Edge Labs E-Commerce Platform

> Update tagline as project evolves

## Project Overview
Update revenue targets, status as they change

## Key Differentiators
Keep this accurate as features are built

## Technology Stack
Add packages as they're installed

## Development Phases
Update checkmarks as phases complete:
- [x] Phase 0: Environment & Foundation
- [ ] Phase 1: Design System
...

## Local Development Setup
Update if setup process changes

## Deployment
Update with actual deployment procedures

## License
Keep current
```

## 🔧 Configuration Documentation

### Environment Variables

Document all `.env` variables in `docs/reference/environment-config.md` (create when needed):

```markdown
# Environment Configuration

## Required Variables

### Application
```
APP_NAME="Pacific Edge Labs"
APP_ENV=production  # local, staging, production
APP_DEBUG=false     # true only in local/staging
APP_URL=https://pacificedgelabs.com
```

### Database
```
DB_CONNECTION=pgsql
DB_HOST=            # Lightsail instance IP or RDS endpoint
DB_PORT=5432
DB_DATABASE=pacific_edge_labs
DB_USERNAME=        # Set in deployment
DB_PASSWORD=        # Set in deployment (use strong password)
```

### AWS Services
```
AWS_ACCESS_KEY_ID=          # IAM user with S3 access only
AWS_SECRET_ACCESS_KEY=      # Store in 1Password
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=pacific-edge-coas
```

### Mail
```
MAIL_MAILER=smtp
MAIL_HOST=                  # SendGrid, Mailgun, etc.
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=orders@pacificedgelabs.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Payment Gateway
```
PAYMENT_GATEWAY=authorize_net  # authorize_net, nmi, mock
AUTHORIZE_NET_LOGIN_ID=        # From merchant account
AUTHORIZE_NET_TRANSACTION_KEY= # From merchant account
AUTHORIZE_NET_MODE=production  # sandbox, production
```

## Optional Variables

### Analytics
```
PLAUSIBLE_DOMAIN=pacificedgelabs.com  # If using Plausible
```
```

## 🔄 Living Documentation Practices

### Regular Reviews

**Weekly:**
- Review open TODO comments
- Update task status in phase documents
- Clean up outdated inline comments

**End of Phase:**
- Update README.md with phase completion
- Create INFO completion report
- Update database schema docs if changed
- Write ADR for major decisions

**Before Handoff:**
- Comprehensive README review
- Verify all environment variables documented
- Ensure deployment procedures tested and documented
- Create deployment checklist

### Documentation Debt

Treat documentation like code debt:

```php
// TODO-DOCS: Document the batch allocation algorithm
// Created: 2025-02-09
// Assigned: Phase 3 cleanup
public function allocate($product, $quantity) {
    // Complex logic here...
}
```

Track doc debt in phase documents:

```markdown
## Documentation Debt (Phase 3)
- [ ] Document batch allocation algorithm in detail
- [ ] Add diagrams to database schema doc
- [ ] Create admin user guide for batch management
- [ ] Document S3 bucket permissions setup
```

## 🎓 Onboarding Documentation

For potential Pacific Edge handoff, maintain `docs/onboarding/`:

### New Developer Guide

**File:** `docs/onboarding/developer-setup.md`

```markdown
# Developer Setup Guide

## Prerequisites
- macOS or Linux (Windows via WSL2)
- Docker Desktop installed
- Git configured

## First-Time Setup

1. Clone repository
2. Copy .env.example to .env
3. Generate app key
4. Start Docker containers
5. Run migrations
6. Seed demo data

[Detailed steps...]

## Common Tasks

### Adding a Product
[Step-by-step...]

### Processing an Order
[Step-by-step...]

### Managing Batches
[Step-by-step...]
```

### Admin User Guide

**File:** `docs/onboarding/admin-guide.md`

For Pacific Edge staff using Filament admin panel.

```markdown
# Admin Panel User Guide

## Logging In
Navigate to /admin and use your credentials.

## Managing Products

### Creating a Product
1. Click "Products" in sidebar
2. Click "Create"
3. Fill in required fields:
   - SKU (must be unique)
   - Name
   - Description (research language, no health claims)
   - Price (in USD)
   - Category
4. Click "Create"

[Screenshots recommended]

## Managing Batches

### Adding a New Batch
[Step-by-step with screenshots]

## Processing Orders

### Marking Order as Shipped
[Step-by-step...]
```

## ✅ Documentation Checklist

Use this checklist when completing a phase:

```markdown
## Phase X Documentation Checklist

- [ ] README.md updated with phase completion
- [ ] All new environment variables documented
- [ ] Database schema doc updated (if applicable)
- [ ] Migration comments added
- [ ] Complex logic has inline comments explaining WHY
- [ ] Public methods have DocBlocks
- [ ] ADR created for major architectural decisions
- [ ] Task completion report (INFO) created
- [ ] TODO comments tracked or resolved
- [ ] Configuration changes documented
- [ ] Deployment procedures updated (if applicable)
- [ ] Admin user guide updated (if admin features added)
```

## 🚀 Quick Reference

### What to Document

| Scenario | Documentation Type | Location |
|----------|-------------------|----------|
| Complex business logic | Inline comments | In the code |
| Public method | DocBlock | Above method |
| New table/migration | Schema doc + comments | `docs/architecture/` + migration file |
| Major architectural decision | Architecture doc | `docs/architecture/` |
| Completed task | Completion report | `docs/Execution/Phase X/INFO-*.md` |
| Phase completion | README update + summary | `README.md` + `docs/history/` |
| Operational reference | Reference guide | `docs/reference/` |
| Feature for admin users | Admin guide | `docs/reference/` (future) |

### Documentation Priority

1. **Critical** (do immediately):
   - Security-sensitive code comments
   - Deployment procedures
   - Environment variable documentation
   - Database migration comments

2. **High** (do before phase completion):
   - Task completion reports
   - README updates
   - DocBlocks for public methods
   - ADRs for major decisions

3. **Medium** (do during cleanup):
   - Inline comments for complex logic
   - Schema documentation
   - Admin user guides

4. **Low** (nice to have):
   - Extensive code examples
   - Architecture diagrams
   - Video walkthroughs

---

**Remember:** Good documentation is like good code—clear, concise, and maintainable. Document for your future self and for Pacific Edge Labs' future development team.
