# [TASK-6-000] Phase 6 Overview: Admin Panel (Filament)

## Purpose
This is a conversational task to plan and generate all Phase 6 tasks with the user. Phase 6 builds the admin panel using Filament to manage products, batches, orders, customers, and compliance data. This is the operational backend that Shane, Eldon, and fulfillment staff will use daily.

## Phase 6 Goals
- Install and configure Filament (if not done in Phase 0)
- Create Filament resources for all major models
- Build dashboard with key metrics
- Implement order management workflow
- Create batch management interface with CoA upload
- Build customer management views
- Implement user roles and permissions
- Create compliance audit log viewer
- Build low stock alerts and notifications
- Design fulfillment workflow (pick, pack, ship)

## Key Decisions Already Made

### What is Filament?
Filament is a Laravel admin panel builder that auto-generates CRUD interfaces, forms, tables, filters, and more from your Eloquent models. It's fast, professional, and highly customizable.

**Why Filament for this project:**
- Saves 1-2 weeks vs building custom admin panel
- Professional UI out of the box
- Handles complex relationships (Product → Batches, Order → OrderItems)
- File uploads (CoA PDFs) are elegant
- Mobile-responsive (check orders on phone)
- Built-in search, filters, pagination
- Customizable when needed

### Filament Structure
**Resources to build:**
1. **ProductResource** - Manage products (CRUD, assign batches, view sales)
2. **BatchResource** - Manage batches (CRUD, upload CoAs, track inventory)
3. **OrderResource** - Manage orders (view, update status, print packing slips, mark shipped)
4. **CustomerResource** - View customers (order history, contact info, compliance logs, **loyalty points balance**)
5. **CategoryResource** - Manage product categories (CRUD, reorder)
6. **ComplianceLogResource** - View compliance attestations (audit trail)
7. **UserResource** - Manage admin users (roles, permissions)
8. **LoyaltyTransactionResource** - View/manage loyalty points (history, manual adjustments)
9. **ReferralResource** - View/manage referrals (active referrals, rewards given)
10. **NewsletterSubscriberResource** - View/export newsletter subscribers

**Dashboard widgets:**
- Today's sales total
- This week's sales total
- Pending orders count
- Low stock batches (< 10 units)
- Recent orders (last 10)
- Top products (by revenue)
- **Total loyalty points issued**
- **Active referrals count**
- **Newsletter subscribers count**

### Who Uses the Admin Panel?
**Roles to consider:**
1. **Super Admin** (Shane/Eldon) - Full access to everything
2. **Fulfillment Staff** - Can view/update orders, print packing slips, mark shipped (no access to products, customers, compliance)
3. **Support** - Can view orders and customers, can't change product data
4. **Future:** Inventory Manager, Marketing, etc.

Use Spatie Laravel Permission (installed in Phase 0) to manage roles.

### Order Fulfillment Workflow
**Admin's daily process:**
1. Log into Filament at `/admin`
2. See "5 pending orders" on dashboard
3. Click "Orders" → Filter by status "Pending"
4. Select orders → Bulk action "Mark as Processing"
5. For each order:
   - View order detail
   - See which batches to pick (batch numbers clearly shown)
   - Print packing slip (PDF with order details, batch info, compliance recap)
   - Pack order
   - Enter tracking number
   - Mark as "Shipped" → triggers shipping email
6. Customer receives tracking email

### Batch Management
**Admin needs to:**
- Create new batch (enter batch #, quantity, test date, purity %)
- Upload CoA PDF to S3
- Assign batch to product
- View batch inventory (available, allocated, sold)
- Deactivate expired batches
- See which orders used which batch (audit trail)

**Filament relationship manager:** Product hasMany Batches (inline batch management on product edit page)

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Filament Installation**
   - "Was Filament installed in Phase 0 or should we install now?"
   - "Custom Filament theme or use default? (default is clean, can customize later)"
   - "Filament panels: one panel for all or separate panels by role?"

2. **Dashboard Design**
   - "Which metrics are most important for Shane/Eldon to see daily?"
     - Sales (today, week, month)?
     - Order count by status?
     - Low stock alerts?
     - Recent activity?
   - "Chart library needed? (Filament has built-in charts)"
   - "Real-time updates or static widgets?"

3. **Product Management**
   - "Can admin create products directly in Filament or should that be restricted?"
   - "Bulk actions needed? (deactivate multiple products, change category, etc.)"
   - "Product preview link (view product page from admin)?"
   - "Import products from CSV/Excel?"

4. **Batch Management**
   - "Inline batch management on product edit or separate batch resource?"
   - "Bulk batch creation from CSV?"
   - "Auto-calculate expiration date from test_date + shelf_life?"
   - "Low stock threshold per batch or global setting?"

5. **Order Management**
   - "Can admin edit order items after order is placed? (usually no, but edge cases)"
   - "Bulk status updates? (mark 10 orders as shipped at once)"
   - "Print packing slips: individual or batch print?"
   - "Refund workflow needed in demo or post-launch?"

6. **Customer Management**
   - "Can admin edit customer addresses, email?"
   - "View customer lifetime value (total spent)?"
   - "Export customer list to CSV?"
   - "Customer notes field for internal comments?"

7. **Compliance Audit**
   - "Compliance log viewer: read-only or can admin delete logs?"
   - "Filter by user, checkpoint type, date range?"
   - "Export compliance logs to PDF/CSV for auditors?"

8. **Permissions & Roles**
   - "Define roles now or just use Super Admin for demo?"
   - "If roles: what can each role do?"
     - Super Admin: everything
     - Fulfillment: orders only
     - Support: orders + customers (read-only)

9. **Loyalty & Referral Management**
   - "Should admin be able to manually adjust points balances? (for customer service)"
   - "View loyalty transaction history per customer?"
   - "Export loyalty points data to CSV?"
   - "Referral tracking: show who referred whom, rewards given?"
   - "Manually mark referrals as complete?"

10. **Newsletter Management**
   - "Export newsletter subscribers to CSV?"
   - "Filter subscribers by subscription date, source?"
   - "View subscriber count on dashboard?"

## Suggested Phase 6 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-6-001:** Filament Installation & Configuration (if not done in Phase 0)
- **TASK-6-002:** Create Filament User (Super Admin)
- **TASK-6-003:** Dashboard Widgets (sales, orders, low stock)
- **TASK-6-004:** ProductResource (CRUD, filters, search)
- **TASK-6-005:** BatchResource or Relationship Manager (manage batches on product)
- **TASK-6-006:** OrderResource (view, update status, bulk actions)
- **TASK-6-007:** CustomerResource (view, order history)
- **TASK-6-008:** CategoryResource (CRUD, reorder)
- **TASK-6-009:** ComplianceLogResource (read-only audit viewer)
- **TASK-6-010:** UserResource (manage admin users)
- **TASK-6-011:** Role & Permission Configuration (Super Admin, Fulfillment, Support)
- **TASK-6-012:** Packing Slip Generator (PDF with order details, batch info)
- **TASK-6-013:** Bulk Order Status Updates
- **TASK-6-014:** Low Stock Alerts (notifications when batch < threshold)
- **TASK-6-015:** CoA Upload Interface (S3 integration in Filament)
- **TASK-6-016:** Order Tracking Number Field & Carrier Link
- **TASK-6-017:** Loyalty Transaction Resource (view points history, manual adjustments)
- **TASK-6-018:** Referral Resource (view referrals, track rewards)
- **TASK-6-019:** Newsletter Subscriber Resource (view, export subscribers)

## AI Prompt Template
```
I'm starting Phase 6 of Pacific Edge Labs - Admin Panel (Filament).

Phase 6 goals:
1. Build Filament admin panel at /admin
2. Create resources for products, batches, orders, customers, categories
3. Build dashboard with key metrics (sales, pending orders, low stock)
4. Implement order fulfillment workflow (view orders, update status, print packing slips, mark shipped)
5. Create batch management interface (upload CoAs, track inventory)
6. Set up roles and permissions (Super Admin, Fulfillment Staff, Support)
7. Build compliance audit log viewer

Context:
- Models already exist from Phases 2-5
- Filament may or may not be installed (check Phase 0)
- Shane/Eldon and fulfillment staff will use this daily
- Order workflow: pending → processing → shipped (with email triggers)
- Batch management is critical (CoA uploads, inventory tracking)
- Compliance audit trail must be viewable (for legal/payment processor audits)

Key workflow:
Admin logs in → Dashboard shows pending orders → Click order → See batch details → Print packing slip → Mark shipped → Tracking email sent

Let's start by confirming Filament installation status. Was it installed in Phase 0?
```

## Important Reminders

### For Filament Setup:
- Filament accessed at `/admin` by default
- Create Super Admin user: `php artisan make:filament-user`
- Protect with middleware (only authenticated users with admin role)
- Custom theme optional (default is professional)
- Mobile-responsive by default (can manage orders from phone)

### For Resources:
- Use Filament generators: `php artisan make:filament-resource Product`
- Define table columns in `table()` method (what shows in list view)
- Define form fields in `form()` method (what shows in create/edit)
- Relationships: use `RelationManager` for hasMany (Product → Batches)
- Actions: define custom actions (print packing slip, send email, etc.)

### For Dashboard:
- Widgets in `app/Filament/Widgets/`
- Use chart widgets for visual data (sales trend, order status breakdown)
- Stat widgets for quick numbers (total sales, pending orders, low stock count)
- Table widgets for recent activity (last 10 orders)
- Refresh interval optional (real-time updates via polling)

### For Order Management:
- Show order number, customer name, status, total, date in table
- Click order → view full details (items, batch info, shipping address)
- Update status via dropdown or custom actions
- Bulk actions: mark selected orders as shipped
- Print packing slip: generate PDF with order details, batch numbers, CoA references
- Tracking number field: text input, auto-detect carrier from format

### For Batch Management:
- Relationship manager on ProductResource (manage batches inline)
- Upload CoA: Filament FileUpload field, store in S3
- Show inventory: available, allocated, sold (calculated fields)
- Low stock indicator: highlight batches with quantity < 10 (configurable)
- Expiration tracking: show days until expiration, highlight expired batches
- Audit trail: which orders used this batch (query order_items)

### For Compliance Audit:
- Read-only resource (no edit/delete)
- Filter by user, checkpoint type (age_verification, researcher_attestation), date range
- Export to CSV or PDF (for auditors, payment processors)
- Show IP address and user agent (proves attestation was legitimate)
- Link to associated order (click log → view order)

### For Roles & Permissions:
- Use Spatie Permission's `role()` middleware on resources
- Super Admin: `->visible(fn () => auth()->user()->hasRole('admin'))`
- Fulfillment Staff: can access OrderResource only, can't delete
- Support: can view OrderResource and CustomerResource (read-only)
- Define permissions: `view_products`, `edit_products`, `delete_products`, etc.

## Success Criteria for Phase 6

At the end of Phase 6, you should have:
- [ ] Filament installed and accessible at `/admin`
- [ ] Super Admin user created and can log in
- [ ] Dashboard showing sales, orders, low stock widgets
- [ ] ProductResource with CRUD, search, filters
- [ ] BatchResource or relationship manager (upload CoAs, manage inventory)
- [ ] OrderResource with status updates, bulk actions
- [ ] CustomerResource with order history
- [ ] CategoryResource with CRUD
- [ ] ComplianceLogResource (read-only audit viewer)
- [ ] UserResource for managing admin users
- [ ] Roles and permissions configured (Super Admin, Fulfillment, Support)
- [ ] Packing slip generator (PDF with batch info)
- [ ] Low stock alerts configured
- [ ] CoA upload working (S3 integration)
- [ ] Tracking number field on orders
- [ ] Admin can complete full order workflow (pending → shipped)
- [ ] **Loyalty transaction history viewable per customer**
- [ ] **Admin can manually adjust loyalty points**
- [ ] **Referral tracking visible (who referred whom, rewards)**
- [ ] **Newsletter subscribers viewable and exportable**

**Shane/Eldon can now manage the entire business from /admin.**

---
**Next Phase:** TASK-7-000 (Polish & Demo Prep)  
**Previous Phase:** TASK-5-000 (Orders & Emails)  
**Phase:** 6 (Admin Panel - Filament)  
**Approach:** Conversational - confirm Filament setup and resource priorities, then build  
**Estimated Duration:** 2-3 days of focused work  
**Priority:** High - operational backend for daily business management
