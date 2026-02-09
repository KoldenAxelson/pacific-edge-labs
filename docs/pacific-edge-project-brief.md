# Pacific Edge Labs - E-commerce Platform Rebuild
## Project Brief for Technical Planning

---

## EXECUTIVE SUMMARY

Pacific Edge Labs is a 2.5-month-old peptide research chemical vendor currently running on Wix. They've hit $100K revenue and are targeting $50M by end of year. Their current platform is functionally adequate but has critical compliance gaps that put their payment processing at risk, poor SEO visibility (critical since they can't advertise on major platforms), and lacks the operational infrastructure to scale.

**Primary Goal:** Build a custom Laravel-based e-commerce platform that can be deployed as a working demo quickly, then go live with real data in minimal time once approved.

**Developer Context:** The developer has just completed VisorPlate (visorplate-us.com), a Laravel e-commerce site using Stripe, ShipStation, MailGun, and Grit. Prefers lean infrastructure ($5 Lightsail or Laravel Forge). Experienced full-stack developer with 20 years experience but new to biotech/pharma compliance requirements.

---

## INDUSTRY CONTEXT: WHY THIS MATTERS

### The Peptide Research Chemical Space
Pacific Edge sells research peptides (GLP-1 agonists like Semaglutide, Tirzepatide, Retatrutide, plus others like BPC-157, TB-500) direct to consumers under the legal framework of "research use only." This is a regulatory gray area:

- Products are NOT FDA-approved drugs
- They're sold as "research chemicals" with disclaimers
- However, enforcement is tightening and compliance requirements are strict
- Payment processors routinely shut down peptide vendors without warning
- Google/Facebook/TikTok ads are essentially banned for this category
- SEO is therefore critical (it's one of the only reliable traffic channels)

### The Payment Processing Problem
This is the biggest existential risk:

- Standard processors (Stripe, PayPal, Square) commonly freeze peptide accounts
- High-risk merchant accounts are required (brokers like VERIFIED, Paycron, PayBlox)
- Underwriters look for specific compliance mechanisms (age gates, checkout confirmations, CoAs)
- Even with proper processors, accounts can be frozen if compliance isn't visible
- Pacific Edge currently has "a special payment processor" but their site lacks enforcement mechanisms

### Current Site Problems
**Pacific Edge's Wix site has:**
- Legal disclaimers in T&C (good)
- No age verification gate (bad)
- No checkout confirmation that buyer is a researcher (bad)
- No "I agree this is research only" checkbox (bad)
- CoAs only available via email request, not on product pages (bad)
- No batch/lot number tracking visible to customers (bad)
- Poor SEO crawlability (mostly images, minimal text)
- Placeholder template text still live on the site

Their legal team just told them "good thing you got us now before it got too bad." So they're aware there's a problem but haven't fixed it yet.

---

## WHAT NEEDS TO BE BUILT

### Core E-commerce Features (Standard)
- Multi-product catalog with categories
- Shopping cart and checkout flow
- Order management and history
- Customer accounts
- Payment processing integration (abstraction layer for multiple processors)
- Shipping integration
- Email notifications (order confirmation, shipping updates)
- Admin panel for product/order/customer management

### Peptide-Specific Features (Differentiators)

**1. Compliance Enforcement (CRITICAL)**
- Age verification gate (21+) with enforcement, not just a disclaimer
- Checkout confirmation checkbox: "I confirm I am a qualified researcher and these products are for laboratory research only"
- Prominent disclaimers on homepage, product pages, and checkout (not buried in T&C)
- Research-only language throughout (never implies human use)

**2. Certificate of Analysis (CoA) Integration (COMPETITIVE ADVANTAGE)**
- Each product has associated batches with individual CoA PDF files
- CoAs visible/downloadable directly on product page (not email-only)
- Database structure: Products → Batches → CoAs
- When batch changes, CoA auto-updates on product page

**3. Batch Traceability (COMPETITIVE ADVANTAGE)**
- Inventory tied to specific batch numbers
- At cart-add, capture which batch_id is being allocated
- Order confirmation shows: "Your Semaglutide 15mg is from Batch #PEL-2025-0142, tested 1/15/2025, 99.3% purity"
- Order history displays batch info for past orders
- "Reorder this exact batch" functionality (if still in stock)

**4. Smart Reorder/Subscription Flows (RETENTION)**
- One-click reorder from order history
- Optional subscription capability (but keep low-key due to compliance concerns)
- Dosing cycle reminders (e.g., "You ordered Semaglutide 30 days ago, time to reorder?")

**5. Educational Content Without Health Claims (TRUST + SEO)**
- Product pages include scientific descriptions of peptide mechanisms
- Links to PubMed papers or published research
- Attribute all claims to external sources ("According to research published in...")
- Never make direct health/wellness/performance claims

**6. SEO-First Architecture (TRAFFIC)**
- Clean, crawlable product pages with structured data
- Descriptive text content (not image-dependent)
- Proper meta tags, alt text, semantic HTML
- Fast page loads
- Mobile responsive

### Technical Architecture Considerations

**Payment Processing:**
- Abstraction layer that supports multiple payment gateways
- Primary: High-risk merchant account gateway (Authorize.Net or similar)
- Secondary: PayPal, Venmo (with understanding they could shut down)
- Tertiary: BNPL options (Affirm, Klarna, Afterpay) as toggleable integrations
- Must be able to swap processors without breaking checkout

**Inventory Management:**
- Products have multiple batches
- Each batch has quantity, CoA file, test date, purity percentage
- Inventory tracking at batch level
- Low stock alerts
- Batch expiration date tracking

**Admin Features:**
- Batch management (add new batch, upload CoA, set quantity)
- Order fulfillment workflow
- Customer management
- Basic analytics (sales, popular products, conversion metrics)

**Compliance Documentation:**
- Audit trail: who bought what, when, which batch
- Ability to generate reports for regulatory purposes if needed
- Terms/Privacy/Refund/Shipping policies easily editable

---

## SUCCESS CRITERIA

### For the Demo (Immediate)
1. Fully functional catalog, cart, and checkout flow
2. All compliance mechanisms working (age gate, checkboxes, disclaimers)
3. CoAs visible on product pages
4. Batch tracking working end-to-end (cart → order → history)
5. Professional design that matches or exceeds Pacific Edge's current Wix aesthetic
6. Works with mock payment processor (abstraction layer in place but not connected)
7. Can be deployed and demoed in browser within 2 weeks

### For Production (Once Approved)
1. Drop in Pacific Edge's real product catalog (30+ SKUs)
2. Connect to actual high-risk payment processor
3. Connect to ShipStation for fulfillment
4. Import any existing customer data (if applicable)
5. Go live in 2 weeks from approval

---

## COMPETITIVE INTELLIGENCE

**Primary Competitor:** Peptide Sciences (peptidesciences.com)
- Premium pricing, fast shipping, third-party testing
- CoAs available but require email request (not on page)
- Standard compliance disclaimers but no visible enforcement mechanisms
- Clean site but nothing innovative

**What Makes Pacific Edge Stand Out:**
1. CoAs directly on product pages (nobody else does this)
2. Visible batch traceability
3. Smart reorder flows
4. Compliance built into UX, not just legal fine print
5. Competitive pricing ($74.99 for 15mg Semaglutide vs higher elsewhere)
6. Educational content that builds authority

---

## CONSTRAINTS & PREFERENCES

**Infrastructure:**
- Prefer lean: $5 Lightsail or Laravel Forge
- Must scale gracefully to handle $50M revenue target
- Fast page loads (critical for SEO and UX)

**Development Speed:**
- Skeleton/demo needed ASAP (target: 1-2 weeks)
- Production-ready in 2 weeks after approval
- Iterative approach: MVP first, polish later

**Tech Stack Baseline:**
- Laravel (developer's expertise)
- Existing tools: ShipStation, MailGun (or similar)
- Payment abstraction (not locked to Stripe)
- Standard relational database (MySQL/PostgreSQL)

**Out of Scope (For Now):**
- Full LIMS (Lab Information Management System)
- Manufacturing/synthesis tracking
- Complex compliance workflow engine
- These were in earlier discussions but are overkill for current needs

---

## NEXT STEPS: INSTRUCTIONS FOR THE NEXT CLAUDE

Your job is to work with the developer to:

1. **Recommend a Tech Stack**
   - Confirm Laravel as the framework (it's what they know)
   - Database choice (MySQL vs PostgreSQL)
   - Frontend approach (Blade templates vs Livewire vs Inertia+Vue)
   - Payment abstraction library recommendations
   - File storage (local vs S3 for CoA PDFs)
   - Email service (MailGun vs alternatives)
   - Deployment approach (Forge vs manual)

2. **Break Into Development Phases**
   Create 4-6 distinct phases that build on each other:
   - Phase 1: Core e-commerce skeleton (catalog, cart, checkout)
   - Phase 2: Compliance layer (age gates, checkboxes, disclaimers)
   - Phase 3: Batch/CoA system
   - Phase 4: Admin panel
   - Phase 5: Polish and production prep
   - (Adjust as needed based on dependencies)

3. **Define Subtasks for Each Phase**
   Within each phase, break down into concrete, actionable tasks:
   - Database migrations to create
   - Models and relationships to build
   - Controllers and routes to implement
   - Views/components to create
   - Third-party integrations to set up
   - Testing checkpoints

4. **Discussion Approach**
   - Ask clarifying questions about technical preferences
   - Explain trade-offs for different architectural choices
   - Keep tasks concrete and time-bound
   - Flag any areas where the developer needs to make decisions
   - Remember: developer is experienced but new to pharma compliance
   - Prioritize speed to demo over perfection

5. **Output Format**
   Produce a structured development plan that includes:
   - Final tech stack recommendation with justification
   - Phase breakdown with time estimates
   - Subtask list for each phase with clear deliverables
   - Critical path items that block other work
   - "Can parallelize" vs "must be sequential" guidance
   - Testing/validation checkpoints

---

## DEVELOPER NOTES

- This is for a family member's business (brother-in-law Chris and his friend Shane/CEO Eldon)
- They're in the "see what happens in the next month or 2" phase
- A working demo will win the project
- They just hired a legal team that's flagging compliance issues
- Payment processor situation is stable but fragile
- They're aware of the problems but haven't solved them yet
- The goal is to position the developer as the obvious solution when their legal team's recommendations meet Wix's limitations

---

## REFERENCE MATERIALS

**Pacific Edge Current Site:** https://www.pacificedgelabs.com/  
**Developer's Recent Build:** https://www.visorplate-us.com/ (Laravel e-commerce, single SKU)

**Key Insight from Earlier Research:**
- Payment processors look for: visible research-only confirmations, age gates, CoAs on pages, clear documentation, domestic sourcing claims
- SEO is critical: ads are banned, organic search is the main traffic source
- Batch traceability and CoA visibility are unmet needs in the market
- Competitors use Shopify or basic platforms that can't do proper batch management

---

## FINAL REMINDER

This is a skeleton/demo project first, production second. Speed matters. The developer can refine and optimize later — right now the goal is to prove the concept and show Shane/Eldon something they can't get from Wix.

The demo should make it immediately obvious that:
1. Compliance is built-in, not bolted-on
2. CoAs are visible (competitors can't do this easily)
3. Batch tracking works seamlessly
4. The UX is professional and trustworthy
5. This scales to their $50M target

Begin by asking the developer about their technical preferences and comfort zones, then build a phase plan that gets to a working demo as fast as possible.
