# [TASK-5-000] Phase 5 Overview: Orders & Emails

## Purpose
This is a conversational task to plan and generate all Phase 5 tasks with the user. Phase 5 builds the order management system, order history pages, and email notification system. This completes the customer-facing purchase flow and prepares for admin fulfillment in Phase 6.

## Phase 5 Goals
- Design orders database schema
- Create Order and OrderItem models
- Build order creation process (from checkout)
- Implement order status workflow (pending → processing → shipped → delivered)
- Create customer order history page
- Build order detail page for customers
- Design email abstraction layer
- Create order confirmation email template
- Build shipping notification email template
- Implement "reorder this exact batch" functionality
- Track batch allocation in order items
- **Build loyalty/rewards points system**
- **Implement referral tracking system**
- **Create newsletter subscription management**

## Key Decisions Already Made

### Order Structure
**Orders table:**
- id, order_number (e.g., "PE-2025-00142")
- user_id (who placed order)
- status (pending, processing, shipped, delivered, cancelled, refunded)
- subtotal, tax, shipping, total
- payment_method, transaction_id
- shipping_name, shipping_address_1, shipping_address_2, shipping_city, shipping_state, shipping_zip, shipping_country
- billing_address (if different from shipping)
- compliance_confirmed_at (when age/researcher checkboxes were checked)
- notes (customer notes, admin notes)
- created_at, updated_at

**Order Items table:**
- id, order_id
- product_id, batch_id (which batch was allocated)
- quantity, price_per_unit, total
- product_snapshot (JSON: name, SKU, purity at time of order - in case product changes)
- batch_snapshot (JSON: batch_number, test_date, purity at time of order)
- created_at, updated_at

**Why snapshots?** Products/batches can change after order is placed. Snapshots preserve what customer actually bought.

### Order Status Workflow
```
pending → processing → shipped → delivered
                    ↓
                cancelled / refunded
```

**Pending:** Order created, payment received (mock), awaiting fulfillment
**Processing:** Admin is picking/packing order
**Shipped:** Order handed to carrier, tracking number assigned
**Delivered:** Customer received (optional, can track via carrier API)
**Cancelled:** Order cancelled before shipping
**Refunded:** Order refunded (full or partial)

### Email System
**Email abstraction decided in Phase 0:**
- Interface: `MailerInterface`
- Dev implementation: MailTrap (catch-all)
- Production: TBD (MailGun, Postmark, SES, etc.)

**Email templates needed:**
1. **Order Confirmation** (sent immediately after order placed)
   - Order number, items, batch info, total, shipping address
   - Compliance confirmation recap ("You confirmed you're a researcher...")
   - Expected ship date
   - Support contact
2. **Shipping Notification** (sent when order status → shipped)
   - Order number, tracking number, carrier
   - Batch info reminder
   - Estimated delivery date
3. **Future:** Abandoned cart, low stock alerts, promotional emails (post-demo)

### Batch Allocation in Orders
**From Phase 4, batch was reserved in cart.**

**When order is placed:**
- Cart items → Order items
- batch_id copied from cart_item to order_item
- Batch.quantity_available decrements by order_item.quantity
- Batch.quantity_allocated decrements (released from cart hold)
- Cart is cleared

**Order shows:**
- "Your Semaglutide 15mg is from Batch #PEL-2025-0142, tested 1/15/2025, 99.3% purity"
- Link to download CoA for that specific batch
- "Reorder this exact batch" button (if batch still in stock)

### Reorder Functionality
**"Reorder this exact batch" feature:**
- Button on order history page next to each order item
- Checks if batch still has inventory
- If yes: adds to cart with same batch_id
- If no: shows message "Batch PEL-2025-0142 is sold out. Current batch: PEL-2025-0198 (99.5% purity)"

**Why this matters:** Researchers want consistency. If Batch A worked well, they want Batch A again.

### Loyalty/Rewards Points System
**Pacific Edge currently has a Wix-based loyalty program** that needs to be replicated and enhanced.

**Loyalty System Requirements:**
- Customers earn points on purchases (e.g., 1 point per $1 spent)
- Points can be redeemed for discounts (e.g., 100 points = $10 off)
- Display points balance on account page
- Show points earned per order
- Track points history (earned, redeemed, expired)
- Optional: Bonus points for referrals, reviews, social shares

**Database structure:**
```
loyalty_transactions: id, user_id, points, type (earned, redeemed, expired), description, order_id, created_at
users: add 'points_balance' column (cached total)
```

**Point earning triggers:**
- Order placed (points = order total)
- Referral successful (bonus points)
- (Future: Reviews, social shares)

**Point redemption:**
- Checkbox at checkout: "Use X points ($Y discount)"
- Deduct points from balance
- Apply discount to order total
- Log transaction

**Why this matters:** Pacific Edge already has customer points balances in Wix. Migrating this data maintains customer continuity and prevents losing goodwill.

### Referral Tracking System
**Pacific Edge has "Refer Friends" functionality** that needs to be preserved.

**Referral System Requirements:**
- Each user gets unique referral code/link (e.g., `pacificedgelabs.com?ref=CHRIS2025`)
- Referred users tracked (who referred whom)
- Referrer gets reward when referee places first order (e.g., 500 bonus points or $10 credit)
- Referee gets reward on signup (e.g., 10% off first order)
- Display referral stats on account page (referrals made, rewards earned)

**Database structure:**
```
referrals: id, referrer_user_id, referee_user_id, code, status (pending, completed), reward_given, created_at
```

**Referral flow:**
1. User visits `/refer-friends`, gets unique code
2. User shares link with friends
3. Friend clicks link, referral code stored in session/cookie
4. Friend creates account, linked to referrer
5. Friend places first order → referrer gets reward

**Why this matters:** Existing customers may have active referral campaigns. Need to preserve referral codes and attribution.

### Newsletter Subscription Management
**Pacific Edge has newsletter signup** on homepage footer.

**Newsletter Requirements:**
- Email capture form (homepage, footer, checkout)
- Store subscriber email and opt-in status
- Sync with email service provider (MailGun, Postmark, etc.)
- Unsubscribe functionality (link in emails)
- Compliance with CAN-SPAM (unsubscribe must work)

**Database structure:**
```
newsletter_subscribers: id, email, subscribed_at, unsubscribed_at, source (homepage, checkout, etc.)
```

**Integration points:**
- Homepage/footer form → create subscriber record
- Checkout checkbox → "Subscribe to newsletter" (optional)
- Sync to email service (MailGun list, Postmark stream, etc.)
- Unsubscribe webhook from email service → update database

**Why this matters:** Existing subscribers need to be migrated. Can't advertise on Google/Facebook, so email list is valuable marketing channel.

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Order Number Format**
   - "Order number format: 'PE-2025-00142' (prefix-year-sequential)?"
   - "Start numbering at 1 or 1000 (looks more established)?"
   - "Include order number in email subject lines?"

2. **Order Status Management**
   - "Who can change order status? (admin only? automated?)"
   - "Send email on every status change or just shipped?"
   - "Track status history? (log every change with timestamp/user)"

3. **Snapshot Strategy**
   - "What data to snapshot in order_items?"
     - Product name, SKU, price
     - Batch number, test date, purity
     - CoA file path (in case it changes)
   - "Snapshots in JSON column or separate snapshot tables?"

4. **Customer Order History**
   - "How many orders to show per page? (10, 20, 50?)"
   - "Filter by status, date range?"
   - "Search by order number, product name?"
   - "Show order items expanded or collapsed?"

5. **Order Detail Page**
   - "What info to show on order detail page?"
     - Order number, status, date
     - Items (with batch info)
     - Shipping address
     - Tracking number (if shipped)
     - Status timeline (ordered → processing → shipped)
     - Reorder buttons per item
   - "Allow customers to cancel pending orders?"

6. **Email Templates**
   - "Use Laravel Blade for email templates or Markdown?"
   - "Include Pacific Edge branding (logo, colors)?"
   - "Plain text version or HTML only?"
   - "Unsubscribe link needed? (transactional emails usually exempt)"

7. **Reorder Logic**
   - "Should reorder be one-click (bypasses cart) or add to cart?"
   - "Reorder entire order or individual items?"
   - "Pre-fill shipping address from original order?"
   - "What if batch is out of stock? (suggest current batch?)"

8. **Tracking Numbers**
   - "Tracking number field on orders table?"
   - "Link to carrier website? (USPS, FedEx, UPS)"
   - "Auto-detect carrier from tracking number format?"

9. **Loyalty/Rewards System**
   - "Points earning rate? (1 point per $1 spent or different?)"
   - "Points redemption rate? (100 points = $10 off?)"
   - "Bonus points for referrals? (how many?)"
   - "Points expiration? (never expire or expire after X months?)"
   - "Display points balance on order history page or separate page?"

10. **Referral System**
   - "Referral code format? (random string, custom codes, user's name?)"
   - "Referrer reward? (points, dollar credit, percentage off?)"
   - "Referee reward? (discount on first order?)"
   - "Where to show referral stats? (account page, dedicated referral dashboard?)"
   - "Referral links shareable via social media or just copy/paste?"

11. **Newsletter Integration**
   - "Newsletter signup on checkout optional or required?"
   - "Double opt-in (confirm email) or single opt-in?"
   - "Sync newsletter subscribers to email service immediately or batch?"
   - "Separate newsletter preferences (product updates, promotions, research news)?"

## Suggested Phase 5 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-5-001:** Database Schema for Orders and Order Items
- **TASK-5-002:** Create Orders and OrderItems Migrations
- **TASK-5-003:** Build Order Model with Relationships
- **TASK-5-004:** Build OrderItem Model with Relationships
- **TASK-5-005:** Order Creation Service (checkout → order)
- **TASK-5-006:** Batch Inventory Decrement on Order (quantity_available update)
- **TASK-5-007:** Cart Clearance After Order
- **TASK-5-008:** Order History Page (customer view)
- **TASK-5-009:** Order Detail Page (customer view)
- **TASK-5-010:** Order Status Workflow Service (status transitions)
- **TASK-5-011:** Email Template: Order Confirmation
- **TASK-5-012:** Email Template: Shipping Notification
- **TASK-5-013:** Send Order Confirmation Email (triggered on order creation)
- **TASK-5-014:** Send Shipping Notification Email (triggered on status → shipped)
- **TASK-5-015:** Reorder Button Component
- **TASK-5-016:** Reorder Service (check batch stock, add to cart)
- **TASK-5-017:** Order Number Generator (unique, sequential)
- **TASK-5-018:** Order Seeder (create sample orders for demo)
- **TASK-5-019:** Loyalty Points Database Schema & Migration
- **TASK-5-020:** Loyalty Points Earning Logic (on order completion)
- **TASK-5-021:** Loyalty Points Redemption (checkout discount)
- **TASK-5-022:** Loyalty Points Display (account page, order history)
- **TASK-5-023:** Referral System Database Schema & Migration
- **TASK-5-024:** Referral Code Generation & Tracking
- **TASK-5-025:** Referral Reward Distribution (points/credits)
- **TASK-5-026:** Referral Dashboard (account page)
- **TASK-5-027:** Newsletter Subscription Database Schema & Migration
- **TASK-5-028:** Newsletter Signup Form Component (footer, checkout)
- **TASK-5-029:** Newsletter Email Service Integration
- **TASK-5-030:** Unsubscribe Flow & Webhook Handler

## AI Prompt Template
```
I'm starting Phase 5 of Pacific Edge Labs - Orders & Emails.

Phase 5 goals:
1. Build order management system (orders, order_items tables)
2. Create order history and detail pages for customers
3. Implement order status workflow (pending → processing → shipped)
4. Build email templates (order confirmation, shipping notification)
5. Implement "reorder this exact batch" functionality
6. Track batch allocation in order items (which batch went to which order)

Context:
- Orders created from checkout flow (Phase 4)
- Each order item tracks product_id AND batch_id (batch traceability)
- Snapshots preserve product/batch data at time of order (immutability)
- Email abstraction layer ready from Phase 0 (MailTrap for dev)
- Customers want to reorder exact same batch (consistency matters)

Order flow:
Cart (Phase 4) → Payment (Phase 4) → Order Created (Phase 5) → Email Sent (Phase 5)

Let's start by finalizing the orders table schema. What data needs to be snapshotted?
```

## Important Reminders

### For Order Schema:
- Order number should be unique, indexed
- Status should be enum or validated string (prevent typos)
- Shipping address fields should match shipping carrier requirements
- Store transaction_id from payment gateway (for refunds/disputes)
- compliance_confirmed_at timestamp proves attestations were checked
- Soft deletes for orders (never hard delete customer data)

### For Order Items:
- Each item links to product_id AND batch_id
- Snapshots prevent data loss if product/batch is deleted
- Store price_per_unit at time of order (price might change later)
- Quantity should match what was in cart (validate this)
- CoA file path in snapshot (if CoA is re-uploaded, order still references original)

### For Batch Inventory:
- Decrement quantity_available when order is placed (not when shipped)
- Release quantity_allocated from cart hold
- Handle race conditions (two users ordering last unit simultaneously)
- Consider queue job for inventory updates (atomic transactions)

### For Order Status:
- Status changes should be logged (status_history table or events)
- Email triggers tied to specific status changes (shipped → send tracking email)
- Admin can manually change status (Filament in Phase 6)
- Consider webhooks for carrier tracking updates (post-demo)

### For Email Templates:
- Use Blade components for reusable email parts (header, footer)
- Test rendering on major email clients (Gmail, Outlook, Apple Mail)
- Plain text fallback for old email clients
- Inline CSS (email clients don't support external stylesheets)
- Personalize with customer name, order details, batch info

### For Reorder Functionality:
- Check batch stock before adding to cart (prevent reordering sold-out batch)
- Show current batch if original batch is out of stock
- Pre-fill shipping address but allow editing (address might have changed)
- One-click reorder is convenient but might surprise users (add to cart safer)
- Track reorder behavior (analytics: which batches get reordered most?)

### For Customer Order Pages:
- Show most recent orders first (order by created_at DESC)
- Paginate (don't load 1000 orders at once)
- Filter by status (show only shipped orders, pending orders, etc.)
- Search by order number or product name
- Mobile-responsive (customers check order status on phone)
- Clear call-to-action: "Track Shipment", "Reorder", "Contact Support"

## Success Criteria for Phase 5

At the end of Phase 5, you should have:
- [ ] Orders table migrated with all necessary fields
- [ ] OrderItems table migrated with batch tracking
- [ ] Order and OrderItem models with relationships
- [ ] Order creation working (checkout → order)
- [ ] Batch inventory decrements on order
- [ ] Cart clears after order placed
- [ ] Order history page showing customer's orders
- [ ] Order detail page with all order info
- [ ] Order status workflow implemented
- [ ] Order confirmation email template
- [ ] Shipping notification email template
- [ ] Emails sending via MailTrap (dev) or production mailer
- [ ] "Reorder this exact batch" button working
- [ ] Reorder service checks batch stock
- [ ] Order number generator (unique, sequential)
- [ ] Demo order seeder creates sample orders
- [ ] Batch traceability visible (customer sees which batch they got)
- [ ] **Loyalty points system working (earn on purchase, redeem at checkout)**
- [ ] **Points balance visible on account page**
- [ ] **Referral system working (unique codes, tracking, rewards)**
- [ ] **Referral dashboard on account page**
- [ ] **Newsletter signup form on footer and checkout**
- [ ] **Newsletter subscribers synced to email service**
- [ ] **Unsubscribe flow working**

**Admin order management not yet built** - that's Phase 6 (Filament). But order data structure is ready.

---
**Next Phase:** TASK-6-000 (Admin Panel - Filament)  
**Previous Phase:** TASK-4-000 (Cart, Checkout & Compliance)  
**Phase:** 5 (Orders & Emails)  
**Approach:** Conversational - finalize order schema and email strategy, then build  
**Estimated Duration:** 2 days of focused work  
**Priority:** High - completes customer-facing purchase flow
