# [TASK-4-000] Phase 4 Overview: Cart, Checkout & Compliance

## Purpose
This is a conversational task to plan and generate all Phase 4 tasks with the user. Phase 4 builds the shopping cart, checkout flow, and critically important compliance mechanisms (age verification, research attestation, logging). This phase determines whether payment processors will approve Pacific Edge's account.

## Phase 4 Goals
- Build shopping cart functionality (Livewire component)
- Implement cart session/persistence
- Create checkout flow (multi-step or single page)
- Build age verification gate (21+ required)
- Implement research attestation checkboxes
- Create compliance logging system
- Build payment abstraction layer with mock gateway
- Integrate batch allocation into cart (from Phase 3)
- Design guest checkout vs required account flow
- Create order preview/confirmation page

## Key Decisions Already Made

### Compliance is NON-NEGOTIABLE
**Why this matters:**
- Payment processors WILL freeze accounts without visible compliance
- Legal team already flagged Pacific Edge's Wix site as insufficient
- Attestation checkboxes must be in checkout flow, not buried in T&C
- Age verification must be enforced, not just a disclaimer

**What payment processors look for:**
1. Age verification gate (can't bypass)
2. "I confirm I am a researcher" checkbox at checkout
3. "These products are for laboratory research only" checkbox
4. Disclaimers visible on product pages AND checkout
5. CoAs on product pages (done in Phase 3)
6. Audit trail of who confirmed what and when

### Account Required for Purchase
**Decision:** Users must create account to purchase.

**Why:**
- Creates compliance audit trail (User #123 confirmed age on DATE at TIME)
- Enables email remarketing (critical since ads are banned)
- Order history = easier reorders = retention
- Better fraud prevention
- Easier customer support

**Implementation:**
- Checkout flow includes account creation inline
- Not a separate "register" page, just email + password during checkout
- Return customers just login

### Cart Architecture
**Session-based vs Database-based?**

**Recommended: Database-based (cart table)**
- Persists across sessions (user can come back later)
- Easier to reserve inventory (cart_items → batch allocation)
- Can show "X people have this in cart" for scarcity
- Simplifies abandoned cart recovery later

**Cart table structure:**
```
carts: id, user_id, session_id (for guests), created_at, updated_at
cart_items: id, cart_id, product_id, batch_id, quantity, created_at, updated_at
```

### Checkout Flow
**Multi-step or single page?**

**Recommended: Multi-step for clarity**
1. **Cart Review** (already built in cart component)
2. **Account/Login** (create account or login)
3. **Age Verification** (modal, must confirm 21+)
4. **Shipping Info** (address, name, phone - supports international)
5. **Payment** (with research attestation checkboxes)
6. **Order Review** (shows batch info, total, confirmations)
7. **Payment Processing** (mock for demo)
8. **Confirmation** (order placed)

Alternative: Single-page with sections (faster but less clear).

### International Shipping Support
**Pacific Edge currently ships internationally** (free shipping on orders $500+).

**Requirements:**
- Country selector in shipping address form
- State/province field (varies by country)
- Postal code validation (different formats per country)
- Phone number format validation (international formats)
- Display shipping costs based on country (free $500+, calculated otherwise)
- Currency display (USD only for demo, multi-currency post-launch)

**Shipping cost calculation:**
- Domestic (USA): Free $150+, calculated rate below
- International: Free $500+, calculated rate below
- Rates from ShipStation or carrier APIs (Phase 8 integration)

**For demo:** Simple country dropdown, flat rate shipping calculator based on country zones.

### Compliance Logging
**What to log:**
- User confirmed age 21+ (timestamp, IP address)
- User confirmed "I am a researcher" (timestamp, IP address)
- User confirmed "Products for research only" (timestamp, IP address)
- Which checkboxes were shown on which order

**Compliance logs table:**
```
compliance_logs: id, user_id, order_id, checkpoint_type, confirmed_at, ip_address, user_agent
```

Checkpoint types: `age_verification`, `researcher_attestation`, `research_only_attestation`

### Payment Abstraction
**For demo: Mock payment gateway**
- Accept any card number, always succeed
- No real charge processing

**For production: High-risk merchant account**
- Authorize.Net, NMI, or similar
- Abstraction layer allows quick swap without touching checkout code

**Payment service interface:**
```php
interface PaymentGatewayInterface {
    public function charge($amount, $cardToken, $orderData);
    public function refund($transactionId, $amount);
    public function verify($transactionId);
}
```

Implementations: `MockPaymentGateway`, `AuthorizeNetGateway`, etc.

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Cart Persistence**
   - "Confirm database-based cart (not session-only)?"
   - "Should cart merge when guest logs in? (if allowed guest browsing)"
   - "Cart expiration? (delete abandoned carts after 30 days?)"

2. **Checkout Flow Design**
   - "Multi-step checkout or single-page?"
   - "Progress indicator for multi-step? (1/6, 2/6, etc.)"
   - "Allow editing previous steps or linear only?"
   - "Save progress between steps (draft order)?"

3. **Age Verification**
   - "When should age gate appear? (first visit? before checkout?)"
   - "Full-page modal or slide-in?"
   - "Store verification in session or database?"
   - "Checkbox 'I confirm I am 21+' or dropdown with birthdate?"

4. **Research Attestation**
   - "Where in checkout should attestation checkboxes appear?"
     - On payment page (alongside card form)
     - Separate step before payment
     - On order review page
   - "Exact wording for checkboxes?" (legal might have specific language)
   - "Make checkboxes required (can't proceed without checking)?"

5. **Account Creation Flow**
   - "When does user create account? (before cart? during checkout?)"
   - "Email verification required? (verify email before order processes?)"
   - "Social login (Google, Facebook) or email/password only?"

6. **Batch Allocation**
   - "When to reserve batch inventory? (add to cart? checkout start? payment success?)"
   - "What if batch sells out during checkout? (switch to next batch automatically?)"
   - "Show batch info in cart? (transparent about which batch customer is getting)"

7. **Payment Interface**
   - "Card form UI: custom-built or Stripe Elements (even for mock)?"
   - "Collect billing address separately from shipping?"
   - "Support multiple payment methods (card, PayPal, BNPL) or just card for demo?"

8. **International Shipping**
   - "Country selector in checkout or assume USA only for demo?"
   - "Shipping cost calculation: flat rates by zone or integrate carrier API?"
   - "Currency: USD only or multi-currency support?"
   - "Address validation for international formats?"

9. **Order Confirmation**
   - "What info to show on confirmation page?"
     - Order number
     - Items purchased (with batch numbers)
     - Total paid
     - Shipping address
     - Expected ship date
   - "Send confirmation email immediately or after fulfillment?"

## Suggested Phase 4 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-4-001:** Database Schema for Cart & Cart Items
- **TASK-4-002:** Create Cart and CartItem Models
- **TASK-4-003:** Cart Livewire Component (add, remove, update quantity)
- **TASK-4-004:** Cart Session Management (merge guest → user cart)
- **TASK-4-005:** Batch Allocation Service (reserve batch when added to cart)
- **TASK-4-006:** Checkout Multi-Step Flow Controller
- **TASK-4-007:** Account Creation/Login Step (inline in checkout)
- **TASK-4-008:** Age Verification Gate Component (modal with logging)
- **TASK-4-009:** Shipping Information Form
- **TASK-4-010:** Payment Form (with research attestation checkboxes)
- **TASK-4-011:** Order Review Page (shows batch info, compliance confirmations)
- **TASK-4-012:** Compliance Logging System
- **TASK-4-013:** Payment Abstraction Layer (interface + mock implementation)
- **TASK-4-014:** Mock Payment Gateway Service
- **TASK-4-015:** Order Confirmation Page
- **TASK-4-016:** Cart Badge in Header (shows item count)
- **TASK-4-017:** Checkout Progress Indicator (if multi-step)
- **TASK-4-018:** International Shipping Support (country selector, address validation)
- **TASK-4-019:** Shipping Cost Calculator (domestic/international rates)

## AI Prompt Template
```
I'm starting Phase 4 of Pacific Edge Labs - Cart, Checkout & Compliance.

This phase is CRITICAL. Payment processors will freeze the account if compliance isn't visible and enforced.

Phase 4 goals:
1. Build shopping cart (Livewire component, database-backed)
2. Create checkout flow (multi-step recommended)
3. Implement age verification gate (21+ required, logged)
4. Add research attestation checkboxes (logged with IP/timestamp)
5. Build payment abstraction layer (mock gateway for demo)
6. Integrate batch allocation from Phase 3 (which batch goes to which order)
7. Create order confirmation page

Context:
- Account creation required for purchase (inline during checkout)
- Age gate must be unavoidable (no close button, must confirm)
- Attestation checkboxes must be in checkout flow (not buried in T&C)
- Everything logged for compliance audit trail
- Batch info shown in cart and order confirmation (transparency)

Payment processors look for:
- Visible age verification
- "I'm a researcher" checkbox
- "Research only" checkbox
- Audit trail of confirmations

Let's start by confirming the cart architecture. Database-backed cart or session-based?
```

## Important Reminders

### For Cart Functionality:
- Livewire makes cart reactive (update without page reload)
- Database-backed carts enable abandoned cart recovery later
- Cart should show batch info (which batch customer is getting)
- Validate quantity against batch availability (can't add more than in stock)
- Handle batch sellout gracefully (reallocate to next batch or remove from cart)

### For Checkout Flow:
- Clear progress indicator (know where in process)
- Save progress between steps (don't lose data on browser refresh)
- Validate each step before proceeding (can't skip shipping if empty)
- Back button should work (can edit previous steps)
- Mobile-responsive (some users will checkout on phone)

### For Compliance:
- Age verification: Store in database with timestamp, not just session
- Attestation checkboxes: Required fields, can't uncheck and proceed
- Logging: Include IP address and user agent for audit trail
- Disclaimers: Visible on every checkout page, not just first
- Legal language: Exact wording matters, confirm with Shane/Eldon's legal team

### For Payment Abstraction:
- Interface defines contract (charge, refund, verify methods)
- Mock gateway always succeeds (for demo testing)
- Production gateway swappable via config (don't hard-code gateway)
- Store transaction ID in orders table (for refunds/disputes)
- Handle gateway errors gracefully (show user-friendly message)

### For Batch Allocation:
- Reserve batch when added to cart (quantity_allocated increments)
- Release batch if removed from cart (quantity_allocated decrements)
- Expire cart reservations after X hours (prevent indefinite holds)
- Lock batch allocation during payment (prevent overselling in race condition)
- Show customer which batch they're getting (transparency builds trust)

### For Order Confirmation:
- Clear order number (for customer support reference)
- Batch info for each product (batch #, purity %, test date)
- Compliance confirmations shown (user confirmed they're researcher, etc.)
- Email confirmation sent (using email abstraction from Phase 0)
- Next steps explained (when will it ship, tracking info coming, etc.)

## Success Criteria for Phase 4

At the end of Phase 4, you should have:
- [ ] Cart table and model created
- [ ] Cart Livewire component working (add, remove, update)
- [ ] Cart shows batch info for each item
- [ ] Batch allocation reserves inventory when added to cart
- [ ] Checkout flow accessible from cart
- [ ] Account creation/login working inline in checkout
- [ ] Age verification gate functioning (required, logged)
- [ ] Shipping information form collecting address
- [ ] Payment form with research attestation checkboxes
- [ ] Compliance logs recording all confirmations
- [ ] Payment abstraction layer built
- [ ] Mock payment gateway processing orders
- [ ] Order confirmation page showing all details
- [ ] Confirmation email sent
- [ ] Cart badge showing item count in header
- [ ] Mobile-responsive checkout flow
- [ ] Can complete full checkout from cart → confirmation

**Orders table and order items not yet built** - that's Phase 5. But order creation logic should be ready.

---
**Next Phase:** TASK-5-000 (Orders & Emails)  
**Previous Phase:** TASK-3-000 (Batch & CoA System)  
**Phase:** 4 (Cart, Checkout & Compliance)  
**Approach:** Conversational - finalize compliance requirements and checkout flow, then build  
**Estimated Duration:** 3-4 days of focused work  
**Priority:** CRITICAL - compliance determines if payment processors approve the account
