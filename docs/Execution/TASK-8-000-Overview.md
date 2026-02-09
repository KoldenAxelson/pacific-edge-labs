# [TASK-8-000] Phase 8 Overview: Production Integration (Post-Approval)

## Purpose
This is a conversational task to plan and generate all Phase 8 tasks with the user. Phase 8 is ONLY executed AFTER Shane/Eldon approve the demo and commit to going live. This phase integrates real payment processing, real email service, ShipStation for fulfillment, migrates real customer data (if any), and hardens security for production traffic.

## Phase 8 Goals
- Integrate real payment processor (high-risk merchant account)
- Connect real email service (MailGun, Postmark, or SES)
- Integrate ShipStation for order fulfillment
- Migrate existing customer data from Wix (if applicable)
- **Migrate loyalty points balances from Wix**
- **Migrate referral tracking data from Wix**
- **Migrate newsletter subscribers from Wix**
- Set up production environment (separate from demo)
- Implement monitoring and error tracking (Sentry, Bugsnag)
- Set up automated backups (database, files)
- Configure domain and SSL certificate
- Implement rate limiting and DDoS protection
- Set up uptime monitoring (Pingdom, UptimeRobot)
- Create runbook for common issues
- Train Shane/Eldon on admin panel
- Go live

## Key Decisions Already Made

### This Phase is Post-Approval
**Do NOT start Phase 8 until:**
- Shane/Eldon have seen and approved the demo
- They've committed to going live with Pacific Edge
- Legal team has reviewed compliance implementation
- Payment processor account is approved and ready

**Why separate from Phase 7:**
- Demo uses mock payment, MailTrap, fake data
- Production requires real integrations, real data, real money
- Approval might not come for weeks/months ("see what happens in next month or 2")
- Keep demo clean and fast, production is separate concern

### Payment Processor Integration
**Pacific Edge uses ONE high-risk payment processor** that supports multiple payment methods (credit cards, PayPal, Affirm, Klarna, Afterpay, Venmo, Apple Pay, etc.).

**This is similar to how Stripe works:** One merchant account, multiple payment methods enabled.

**Common high-risk gateways that work this way:**
- Authorize.Net (with advanced integration)
- NMI (Network Merchants Inc)
- Braintree (PayPal's platform)
- VERIFIED
- Others

**What you need from Shane/Eldon:**
- Gateway name and API credentials (merchant ID, API key, etc.)
- Which payment methods are enabled (cards, PayPal, BNPL)
- Gateway documentation (API endpoints, integration guide)
- Test environment credentials (for testing before going live)
- Underwriter requirements (any specific compliance they want to see)

**Integration approach:**
- The payment abstraction layer built in Phase 4 already supports this
- Swap `MockPaymentGateway` with real gateway implementation (e.g., `AuthorizeNetGateway`)
- Gateway handles all payment methods (cards, PayPal, Klarna, etc.) through single API
- No checkout code changes needed (abstraction layer handles it)
- Test in sandbox mode first
- Verify PCI compliance requirements (likely using tokenization, not storing cards)

### Email Service Integration
**Pacific Edge currently uses "some mailer service"** (unknown which).

**Options:**
- **MailGun** (user is familiar, reliable, moderate cost)
- **Postmark** (best deliverability, higher cost)
- **Amazon SES** (cheapest, good deliverability, more setup)

**What you need:**
- API credentials from chosen service
- Sending domain verified (emails from @pacificedgelabs.com)
- SPF/DKIM records configured (prevent spam filtering)
- Templates migrated from MailTrap to production service

**Integration approach:**
- Swap email driver in `.env` (from `mailtrap` to `mailgun` or `ses`)
- Test emails in staging environment
- Verify deliverability (check spam folders, deliverability reports)

### ShipStation Integration
**Pacific Edge likely uses ShipStation** (or similar: ShipBob, Shippo, EasyPost).

**ShipStation features:**
- Import orders from Pacific Edge via API
- Print shipping labels
- Track shipments
- Update order status in Pacific Edge when shipped

**Integration approach:**
- ShipStation has REST API and webhooks
- Pacific Edge pushes new orders to ShipStation
- ShipStation webhook updates order status in Pacific Edge (mark as shipped, add tracking number)
- Can also use ShipStation's label generation API directly in Filament admin

**What you need:**
- ShipStation API credentials
- Carrier accounts (USPS, FedEx, UPS) configured in ShipStation
- Webhook endpoint in Pacific Edge (e.g., `/webhooks/shipstation`)

### Data Migration from Wix
**IF Pacific Edge has existing customers/orders in Wix:**
- Export customer data (names, emails, addresses)
- Export order history (products, dates, amounts)
- Import into Pacific Edge database
- Match products by SKU (if SKUs are consistent)

**Challenges:**
- Wix export format might be messy (CSV cleanup required)
- Password migration not possible (users must reset passwords)
- Order history might be incomplete (no batch tracking in Wix)

**Decision:** Confirm with Shane/Eldon if data migration is needed. If not, start fresh.

### Production Environment
**Separate from demo environment:**

**Demo (Phases 0-7):**
- URL: demo.pacificedgelabs.com (or Lightsail IP)
- Mock payment, MailTrap emails, fake data
- Used for client approval and testing

**Production (Phase 8):**
- URL: www.pacificedgelabs.com
- Real payment, real emails, real customer data
- Live traffic, real orders

**Infrastructure:**
- Start with same Lightsail instance (can scale to EC2 later)
- Or set up new Lightsail/EC2 for production
- Separate database (don't mix demo and production data)
- Separate S3 bucket for production CoAs
- Automated backups configured

### Security Hardening for Production
**Additional security beyond Phase 7:**
- **Rate limiting:** Aggressive limits on login, checkout, API endpoints (prevent brute force, DDoS)
- **WAF (Web Application Firewall):** AWS WAF or Cloudflare (block malicious traffic)
- **DDoS protection:** Cloudflare or AWS Shield (if budget allows)
- **Intrusion detection:** Monitor for suspicious activity (unusual traffic patterns, SQL injection attempts)
- **Secrets management:** Rotate API keys regularly, use AWS Secrets Manager (if on EC2)
- **Database access:** Restrict to application only (no public access)
- **Admin panel:** IP whitelist (only Shane/Eldon's office IP can access /admin) or 2FA

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Approval Confirmation**
   - "Has Shane/Eldon approved the demo and committed to going live?"
   - "Has legal team reviewed and approved compliance implementation?"
   - "Is payment processor account approved and active?"

2. **Payment Processor Details**
   - "Which high-risk payment gateway is Pacific Edge using?"
   - "Do you have API credentials (merchant ID, API key)?"
   - "Is there a test/sandbox environment for testing?"
   - "Any specific underwriter requirements to implement?"

3. **Email Service**
   - "Which email service should we use? (MailGun, Postmark, SES, other)"
   - "Do you have API credentials for the chosen service?"
   - "Is sending domain verified? (emails from @pacificedgelabs.com)"
   - "SPF/DKIM records configured in DNS?"

4. **ShipStation**
   - "Is ShipStation confirmed or different service?"
   - "Do you have ShipStation API credentials?"
   - "Which carriers are configured? (USPS, FedEx, UPS)"
   - "Should we push orders to ShipStation automatically or manually?"

5. **Data Migration**
   - "Are there existing customers/orders in Wix to migrate?"
   - "If yes, how many customers? How many orders?"
   - "Do you have Wix data export (CSV or database backup)?"
   - "Start fresh or import historical data?"
   - "Loyalty points balances: how many customers have points?"
   - "Referral data: active referrals that need to be preserved?"
   - "Newsletter subscribers: how many subscribers to migrate?"

6. **Production Environment**
   - "Use same Lightsail instance or set up new production server?"
   - "Domain ready? (www.pacificedgelabs.com)"
   - "DNS records configured to point to production server?"
   - "SSL certificate needed or already in place?"

7. **Monitoring & Backups**
   - "Which error tracking service? (Sentry, Bugsnag, Rollbar)"
   - "Database backup frequency? (daily, weekly, real-time replication)"
   - "File backup (S3 CoAs) needed or S3 versioning sufficient?"
   - "Uptime monitoring? (Pingdom, UptimeRobot, New Relic)"

8. **Security Hardening**
   - "IP whitelist for admin panel? (restrict to office IP only)"
   - "2FA for admin users?"
   - "WAF/DDoS protection? (Cloudflare, AWS WAF)"
   - "Rate limiting thresholds? (how many login attempts, API calls?)"

## Suggested Phase 8 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-8-001:** Obtain Payment Processor Credentials
- **TASK-8-002:** Implement Real Payment Gateway (replace MockPaymentGateway)
- **TASK-8-003:** Test Payment Integration in Sandbox
- **TASK-8-004:** Obtain Email Service Credentials
- **TASK-8-005:** Configure Real Email Service (MailGun/Postmark/SES)
- **TASK-8-006:** Verify Sending Domain & SPF/DKIM Records
- **TASK-8-007:** Migrate Email Templates to Production Service
- **TASK-8-008:** Obtain ShipStation API Credentials
- **TASK-8-009:** Implement ShipStation Order Push
- **TASK-8-010:** Implement ShipStation Webhook (update order status)
- **TASK-8-011:** Export Customer Data from Wix (if applicable)
- **TASK-8-012:** Import Customer Data into Production Database
- **TASK-8-013:** Export Order History from Wix (if applicable)
- **TASK-8-014:** Import Order History into Production Database
- **TASK-8-015:** Export Loyalty Points Data from Wix
- **TASK-8-016:** Import Loyalty Points Balances into Production Database
- **TASK-8-017:** Export Referral Data from Wix
- **TASK-8-018:** Import Referral Tracking into Production Database
- **TASK-8-019:** Export Newsletter Subscribers from Wix
- **TASK-8-020:** Import Newsletter Subscribers into Production Database
- **TASK-8-021:** Set Up Production Server (Lightsail or EC2)
- **TASK-8-022:** Configure Production Database (separate from demo)
- **TASK-8-023:** Configure Production S3 Bucket
- **TASK-8-024:** Configure Domain & DNS Records
- **TASK-8-025:** Install SSL Certificate (Let's Encrypt or purchased)
- **TASK-8-026:** Set Up Error Tracking (Sentry/Bugsnag)
- **TASK-8-027:** Configure Database Backups (automated, daily)
- **TASK-8-028:** Set Up Uptime Monitoring (Pingdom/UptimeRobot)
- **TASK-8-029:** Implement Rate Limiting (login, checkout, API)
- **TASK-8-030:** Configure WAF/DDoS Protection (optional)
- **TASK-8-031:** IP Whitelist Admin Panel (optional)
- **TASK-8-032:** Enable 2FA for Admin Users (optional)
- **TASK-8-033:** Create Production Runbook (common issues, troubleshooting)
- **TASK-8-034:** Train Shane/Eldon on Admin Panel
- **TASK-8-035:** Final Pre-Launch Checklist
- **TASK-8-036:** Go Live

## AI Prompt Template
```
I'm starting Phase 8 of Pacific Edge Labs - Production Integration.

IMPORTANT: This phase is only executed AFTER Shane/Eldon approve the demo and commit to going live.

Phase 8 goals:
1. Integrate real payment processor (high-risk merchant account)
2. Connect real email service (MailGun/Postmark/SES)
3. Integrate ShipStation for fulfillment
4. Migrate existing customer data from Wix (if applicable)
5. Set up production environment (separate from demo)
6. Implement monitoring and error tracking
7. Configure automated backups
8. Harden security for production traffic
9. Train Shane/Eldon on admin panel
10. Go live

Context:
- Demo is approved and Shane/Eldon are ready to launch
- Payment processor account is approved (need credentials)
- Need to integrate real services (no more mocks)
- Production will have real customer data, real orders, real money
- Must be rock-solid reliable (this is their business)

First question: Which payment gateway is Pacific Edge using? (Authorize.Net, NMI, PayBlox, other?)
```

## Important Reminders

### For Payment Integration:
- **NEVER store credit card numbers** (PCI compliance nightmare)
- Use tokenization (gateway provides token, you store token)
- Test thoroughly in sandbox before going live
- Handle all gateway error codes gracefully (card declined, insufficient funds, etc.)
- Log all payment transactions (for reconciliation, disputes)
- Refund process tested and working

### For Email Integration:
- Test deliverability in spam folders (Gmail, Outlook, Yahoo)
- Use plain text version (some email clients strip HTML)
- Inline CSS (email clients don't support external stylesheets)
- SPF/DKIM/DMARC configured (improves deliverability)
- Monitor bounce rate (high bounce = bad sender reputation)
- Unsubscribe link (even for transactional emails, best practice)

### For ShipStation Integration:
- Test order push (create order in Pacific Edge → appears in ShipStation)
- Test webhook (mark shipped in ShipStation → updates Pacific Edge)
- Handle errors (ShipStation API down, webhook fails, etc.)
- Retry logic for failed API calls
- Log all ShipStation interactions (for debugging)

### For Data Migration:
- **Backup before importing** (don't corrupt production database)
- Validate data before import (check for duplicates, malformed data)
- Map Wix fields to Pacific Edge fields (might not be 1:1)
- Test with small batch first (10 customers, not 1000)
- Customers will need to reset passwords (can't migrate hashed passwords from Wix)
- Notify customers of migration (email: "We've upgraded our site, please reset your password")

### For Production Environment:
- **Separate database** (never mix demo and production)
- Environment variables secured (production .env is different from demo)
- Debug mode OFF in production (never show stack traces to users)
- Logging configured (errors logged to file or service)
- Queue workers running (for email sending, batch processing)
- Cron jobs configured (queue workers, batch expiration checks, backups)

### For Monitoring:
- **Error tracking:** Catch and log all exceptions (Sentry alerts you in real-time)
- **Uptime monitoring:** Get notified if site goes down (Pingdom, UptimeRobot)
- **Performance monitoring:** Track slow queries, slow pages (New Relic, Scout APM)
- **Database monitoring:** Track query performance, slow queries
- **Server monitoring:** CPU, RAM, disk usage (AWS CloudWatch, Grafana)

### For Backups:
- **Database backups:** Daily automated backups, retained for 30 days
- **File backups:** S3 versioning enabled (CoA PDFs, product images)
- **Test restore process** (backups are useless if you can't restore)
- **Off-site backups:** Store backups in different region (disaster recovery)

### For Security:
- **Rate limiting:** Prevent brute force login (5 attempts → lockout), checkout spam
- **WAF:** Block common attack patterns (SQL injection, XSS)
- **DDoS protection:** Cloudflare or AWS Shield (if budget allows)
- **Admin panel:** IP whitelist or 2FA (prevent unauthorized access)
- **Secrets rotation:** Change API keys, database passwords regularly
- **Security headers:** HSTS, CSP, X-Frame-Options (prevent clickjacking)
- **Vulnerability scanning:** Run OWASP ZAP or similar (find security holes)

### For Training:
- Walk Shane/Eldon through admin panel (show them how to manage orders, products, batches)
- Create video tutorial or written guide (for future reference)
- Cover common tasks: create product, upload CoA, mark order shipped, etc.
- Show them where to find help (support contact, documentation)

## Success Criteria for Phase 8

At the end of Phase 8, you should have:
- [ ] Real payment processor integrated and tested
- [ ] Real email service configured and sending emails
- [ ] ShipStation integration working (order push, webhook)
- [ ] Customer data migrated from Wix (if applicable)
- [ ] **Loyalty points balances migrated and reconciled**
- [ ] **Referral tracking data migrated**
- [ ] **Newsletter subscribers migrated**
- [ ] Production server set up and running
- [ ] Production database separate from demo
- [ ] Production S3 bucket configured
- [ ] Domain configured and pointing to production server
- [ ] SSL certificate installed (HTTPS working)
- [ ] Error tracking configured (Sentry/Bugsnag)
- [ ] Database backups automated (daily, tested restore)
- [ ] Uptime monitoring configured (Pingdom/UptimeRobot)
- [ ] Rate limiting implemented
- [ ] Security hardening completed (WAF, IP whitelist, 2FA)
- [ ] Runbook created (common issues, troubleshooting)
- [ ] Shane/Eldon trained on admin panel
- [ ] Final pre-launch checklist completed
- [ ] **SITE IS LIVE** at www.pacificedgelabs.com

**Pacific Edge Labs is now live and accepting real orders.**

---
**Previous Phase:** TASK-7-000 (Polish & Demo Prep)  
**Phase:** 8 (Production Integration - Post-Approval)  
**Approach:** Conversational - confirm approval and gather credentials, then integrate  
**Estimated Duration:** 1-2 weeks of focused work (depends on data migration complexity)  
**Priority:** CRITICAL - this is the final phase before real business operations begin
