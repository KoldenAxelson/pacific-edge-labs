# Pacific Edge Labs E-Commerce Platform

> **Premium peptide research chemical vendor platform with industry-leading compliance and batch traceability**

A custom-built Laravel e-commerce solution designed specifically for the peptide research chemical industry, featuring advanced compliance enforcement, certificate of analysis (CoA) integration, and end-to-end batch traceability.

## 🎯 Project Overview

Pacific Edge Labs is a rapidly growing peptide research vendor ($100K revenue in 2.5 months, targeting $50M) that required a custom platform to replace their Wix-based storefront. The existing solution had critical compliance gaps that threatened payment processor relationships and lacked the operational infrastructure needed for scale.

This platform prioritizes:
- **Payment processor compliance** - Visible enforcement mechanisms that satisfy high-risk merchant underwriters
- **Batch-level traceability** - Full transparency from purchase to certificate of analysis
- **Regulatory defensibility** - Comprehensive audit trails and attestation logging
- **SEO-first architecture** - Critical for organic discovery (paid ads banned in this industry)
- **Operational scalability** - Built to handle 50M+ in annual revenue

## ✨ Key Differentiators

### 1. Compliance Enforcement (Payment Processor Requirement)
- **Age verification gate** - Mandatory 21+ confirmation with IP logging
- **Research attestation** - Checkout checkboxes: "I am a qualified researcher" / "Products for research only"
- **Compliance audit trail** - Every confirmation logged with timestamp, IP, user agent
- **Prominent disclaimers** - Visible on homepage, product pages, cart, and checkout (not buried in T&C)

### 2. Certificate of Analysis Integration (Competitive Advantage)
- CoAs displayed directly on product pages (competitors require email request)
- S3-hosted PDF storage with direct download
- CoAs linked to specific batches, not generic product listings
- Automatic CoA updates when batch inventory changes

### 3. Batch Traceability System (Industry-First)
- Inventory tracked at batch level, not just product level
- Order confirmation shows: *"Your Semaglutide 15mg is from Batch #PEL-2025-0142, tested 1/15/2025, 99.3% purity"*
- Order history displays batch information for past purchases
- "Reorder exact batch" functionality (if still in stock)
- Batch expiration tracking with automatic deactivation

### 4. Payment Processor Abstraction
- Swappable gateway architecture (critical for high-risk merchant accounts)
- Interface-based design supports multiple processors without code changes
- Mock gateway for demo, production-ready for Authorize.Net, NMI, PayBlox, etc.
- Transaction logging for refunds and dispute resolution

## 🛠 Technology Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.5)
- **Database:** PostgreSQL 18
- **Authentication:** Laravel Breeze with Alpine.js
- **Admin Panel:** Filament 4 (beta) with Filament Shield
- **API:** Laravel Sanctum (for future mobile app)
- **Permissions:** Spatie Laravel Permission
- **Debugging:** Laravel Telescope (development only)

### Frontend
- **Templating:** Blade
- **CSS:** Tailwind CSS 4
- **JavaScript:** Alpine.js (lightweight, reactive)
- **Components:** Livewire 3 (server-rendered reactivity)
- **Build Tool:** Vite

### Infrastructure
- **Hosting:** AWS Lightsail ($5/month tier)
- **Storage:** AWS S3 (CoA PDFs, product images)
- **Email:** Abstraction layer (MailTrap for dev, swappable for production)
- **Queue:** Database-backed (Redis optional for production)
- **Local Development:** Laravel Sail (Docker)

### Third-Party Integrations (Phase 8)
- **Shipping:** ShipStation
- **Payments:** High-risk merchant processor (TBD by Pacific Edge)
- **Analytics:** Privacy-focused (Plausible/Fathom recommended)

## 🏗 Architecture Decisions

### Why Database-Backed Carts?
- Persist across sessions (users can return later)
- Enable abandoned cart recovery
- Simplify inventory reservation during checkout
- Support "X people have this in cart" scarcity messaging

### Why Batch-Level Inventory?
- Regulatory requirement: customers must know which CoA applies to their purchase
- Competitive advantage: full transparency builds trust
- Operational efficiency: FIFO inventory rotation prevents expiration waste
- Compliance benefit: complete audit trail from batch to customer

### Why Account Required for Purchase?
- Creates compliance audit trail (User #123 confirmed age on DATE at TIME)
- Enables email remarketing (critical since paid ads are banned)
- Facilitates repeat purchases and dosing cycle reminders
- Reduces fraud and chargebacks
- Simplifies customer support

### Why Payment Abstraction Layer?
- Payment processors frequently freeze peptide vendor accounts
- High-risk merchant accounts switch providers when rates change
- Abstraction allows swapping gateways without touching checkout code
- Interface-based design future-proofs the codebase

## 📋 Development Phases

### Phase 0: Environment & Foundation ✅
- Laravel 12 project initialization with Sail
- PostgreSQL, Redis, Meilisearch configuration
- Authentication (Breeze), admin panel (Filament), debugging (Telescope)
- Tailwind, Alpine.js, Livewire setup
- Payment and email abstraction layers
- Testing framework and seeders

### Phase 1: Design System & Brand ✅
- Thorne-inspired clinical aesthetic (Deep Navy primary, Cyan accent, Amber compliance)
- Typography: DM Sans headings, Inter body, JetBrains Mono data values
- 34 reusable Blade components (buttons, cards, badges, forms, compliance UI, CoA display)
- Animation vocabulary with two-phase choreography and reduced-motion support
- `/design` showcase route for client presentation
- Responsive layout foundations with mobile drawer navigation

### Phase 2: Product Catalog (Up Next)
- Product and category models with relationships
- Public product listing and detail pages
- Search and filter functionality
- Admin CRUD via Filament
- SEO-optimized product pages

### Phase 3: Batch & CoA System
- Batch-level inventory tracking
- S3 integration for CoA PDF storage
- CoA display on product pages
- FIFO batch allocation logic
- Low stock alerts and expiration tracking
- Batch management in Filament admin

### Phase 4: Cart, Checkout & Compliance
- Database-backed shopping cart
- Multi-step checkout flow
- Age verification gate (21+ with logging)
- Research attestation checkboxes
- Compliance logging system
- International shipping support
- Mock payment gateway

### Phase 5: Orders & Customer Management
- Order processing and fulfillment workflow
- Order history and tracking
- Email notifications (confirmation, shipping updates)
- Customer account dashboard
- Reorder functionality with batch preference

### Phase 6: Admin Dashboard (Filament)
- Comprehensive product, batch, and inventory management
- Order processing and fulfillment tools
- Customer management and support
- Analytics dashboard (sales, conversion, popular products)
- Compliance report generation

### Phase 7: Polish & Demo Preparation
- Legal pages (Terms, Privacy, Refund Policy, Shipping Policy)
- SEO optimization (meta tags, structured data, sitemap)
- Performance tuning (query optimization, caching, image optimization)
- Security hardening (rate limiting, CAPTCHA, XSS/CSRF protection)
- Comprehensive demo data seeding
- Mobile responsiveness verification
- Accessibility audit (WCAG AA)

### Phase 8: Production Integration (Post-Approval)
- Real product catalog import
- Live payment processor integration
- ShipStation fulfillment connection
- Customer data migration (if applicable)
- SSL certificate installation
- Domain configuration
- Production deployment

## 🚀 Local Development Setup

### Prerequisites
- **Docker Desktop** (for Laravel Sail)
- **Git**
- **~2GB** free disk space

### Installation

```bash
# Clone repository
git clone git@github.com:KoldenAxelson/pacific-edge-labs.git
cd pacific-edge-labs

# Start Docker containers (first run takes 5-10 minutes)
./vendor/bin/sail up -d

# Create shell alias for convenience (optional but recommended)
alias sail='./vendor/bin/sail'

# Run database migrations
sail artisan migrate

# Seed demo data (when available)
sail artisan db:seed

# Start Vite dev server for frontend assets
sail npm install
sail npm run dev
```

Access the application at **http://localhost**

### Common Commands

```bash
# Container management
sail up -d              # Start containers in background
sail down               # Stop containers
sail restart            # Restart containers

# Laravel Artisan
sail artisan migrate    # Run migrations
sail artisan tinker     # Interactive shell
sail artisan test       # Run test suite

# Package management
sail composer install   # Install PHP dependencies
sail npm install        # Install JavaScript dependencies

# Frontend build
sail npm run dev        # Development with hot reload
sail npm run build      # Production build

# Database
sail psql               # PostgreSQL shell
sail artisan migrate:fresh --seed  # Fresh database with seed data

# Debugging
sail artisan telescope:install     # Access at /telescope
```

## 🧪 Testing

```bash
# Run all tests
sail artisan test

# Run specific test file
sail artisan test --filter=PaymentServiceTest

# Run specific test method
sail artisan test --filter=test_users_can_authenticate

# Run with coverage (requires Xdebug)
sail artisan test --coverage
```

### Current Test Coverage (Through Phase 1)
- **Authentication:** Registration, login, logout, password reset, email verification
- **Role/Permissions:** Role assignment, permission checks, role-based access
- **Admin Panel:** Filament access gated by role
- **Payment Service:** Charge, refund, card masking, error handling
- **Design System:** `/design` route renders without errors or database queries

### Coverage Goals
- **Authentication:** 100%
- **Role/Permissions:** 100%
- **Payment Service:** 90%+
- **Overall:** 80%+ coverage
- **Critical paths (checkout, payments):** 100% when implemented

## 📁 Project Structure

```
├── app/
│   ├── Console/Commands/   # Custom Artisan commands (SeedDemo, SeedDevelopment)
│   ├── Contracts/          # Interfaces (PaymentGatewayInterface, EmailServiceInterface)
│   ├── Filament/           # Filament admin panel resources and pages
│   ├── Http/
│   │   ├── Controllers/    # Route controllers (Auth, Profile)
│   │   └── Requests/       # Form request validation
│   ├── Livewire/           # Livewire components
│   ├── Mail/               # Mailable classes (TestEmail)
│   ├── Models/             # Eloquent models (User, PaymentTransaction)
│   ├── Policies/           # Authorization policies (RolePolicy)
│   ├── Providers/          # Service providers (App, Telescope)
│   ├── Services/           # Business logic (PaymentService, EmailService, StorageService)
│   ├── Traits/             # Reusable traits (SeederHelpers)
│   └── View/Components/    # Blade layout components
├── database/
│   ├── factories/          # Model factories for testing
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders (Role, User, placeholders for future)
├── docs/
│   ├── architecture/       # Architecture decision docs (payment, email)
│   ├── Execution/          # Phase task files (TASK-*) and completion reports (INFO-*)
│   │   ├── Phase 0/        # 10 tasks + 10 completion reports (complete)
│   │   ├── Phase 1/        # 15 tasks + 16 completion reports (complete)
│   │   ├── Phase 2/        # Product Catalog (up next)
│   │   └── TASK-X-000-Overview.md  # Phase overview files (3–8)
│   ├── Guides and Templates/ # Coding conventions, documentation guide, templates
│   ├── history/            # Project brief, phase completion summaries
│   └── reference/          # Testing guide, seeding guide, color reference
├── resources/
│   ├── css/                # Tailwind CSS + design tokens + animation keyframes
│   ├── js/                 # Alpine.js + Collapse plugin
│   └── views/              # Blade templates
│       ├── components/     # 34 reusable Blade components (ui/, product/, coa/, compliance/, design/)
│       ├── layouts/        # App shell, guest, navigation
│       └── design.blade.php # /design showcase route
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes (future)
└── tests/
    ├── Feature/            # Feature tests (Auth, Roles, Admin access)
    ├── Helpers/            # Test helper traits
    └── Unit/               # Unit tests (PaymentService)
```

## 🔐 Security Features

**Implemented (Phases 0–1):**
- **CSRF protection** (Laravel default)
- **XSS protection** (escaped output in Blade)
- **SQL injection prevention** (Eloquent ORM, no raw queries with user input)
- **Environment variables** never committed (.env in .gitignore)
- **Role-based access control** (Spatie Permission with three roles)
- **Payment data** never fully stored (last 4 digits only, PCI-aware)
- **Admin panel** gated to admin+ roles via Filament Shield
- **Age gate UI component** built (enforcement logic in Phase 4)
- **Research attestation UI** built (checkout integration in Phase 4)
- **Disclaimer banners** on every page via app layout

**Planned (Future Phases):**
- **Age verification gate** enforcement with IP logging for compliance (Phase 4)
- **Research attestation checkboxes** at checkout (Phase 4)
- **Rate limiting** on authentication and checkout endpoints (Phase 7)
- **CAPTCHA** on age verification gate (Phase 7)
- **SSL/HTTPS enforcement** in production (Phase 8)

## 🎨 Design System (Phase 1 — Complete)

Thorne-inspired clinical aesthetic. White surfaces, structured layouts, data-forward presentation. The `/design` route showcases all components live.

- **Colors:** Deep Navy `#0F172A` primary, Cyan `#06B6D4` accent (sparingly), Amber for compliance badges, Off-white `#F8F9FA` backgrounds
- **Typography:** DM Sans 600 headings, Inter body, JetBrains Mono for data values (purity %, batch numbers)
- **Components (34 total):** Buttons (pill-shaped), product cards (hover blur reveal), CoA accordion, age gate, attestation checkboxes, disclaimer banners, alerts, toasts, badges, form elements, navigation, footer
- **Animation:** Seven named classes with two-phase choreography (container first, content follows). All disabled via `prefers-reduced-motion`
- **Layouts:** `app.blade.php` (authenticated), `guest.blade.php` (auth pages), responsive navigation with mobile drawer

## 📊 Database Schema

**Existing (Phase 0):**
```
users                 # Customer accounts with authentication
roles                 # Super-admin, Admin, Customer (Spatie)
permissions           # Granular permission definitions (Spatie)
payment_transactions  # Payment processing audit trail
```

**Planned (Future Phases):**
```
products              # Product catalog (Phase 2)
categories            # Product categories (Phase 2)
batches               # Batch-level inventory with CoA (Phase 3)
carts                 # Persistent shopping carts (Phase 4)
cart_items            # Cart line items with batch allocation (Phase 4)
orders                # Order history (Phase 5)
order_items           # Order line items with batch info (Phase 5)
compliance_logs       # Age verification, attestation logging (Phase 4)
```

## 🌐 Deployment

### Demo Environment (AWS Lightsail)
```bash
# SSH into Lightsail instance
ssh ubuntu@YOUR_LIGHTSAIL_IP

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.5-fpm
sudo systemctl restart nginx
```

### Production Checklist
- [ ] Environment variables configured (.env)
- [ ] Database migrations run
- [ ] S3 buckets created and permissions set
- [ ] SSL certificate installed
- [ ] Domain DNS configured
- [ ] Payment processor credentials added
- [ ] ShipStation API connected
- [ ] Email service configured
- [ ] Analytics tracking installed
- [ ] Error monitoring enabled (Sentry/Bugsnag)
- [ ] Backups configured (database + S3)

## 📈 Performance Optimizations

- **Query optimization:** Eager loading relationships (no N+1 queries)
- **Database indexing:** Proper indexes on frequently queried columns
- **Caching:** Redis for session/cache (database fallback)
- **Image optimization:** WebP format, lazy loading, responsive srcsets
- **Code splitting:** Vite-based asset bundling
- **CDN:** CloudFront for static assets (optional)
- **Opcache:** PHP opcache enabled in production

## 🤝 Contributing

This is a proprietary project developed for Pacific Edge Labs. I'm not interested in collaborative work for this project.

## 👨‍💻 Developer

**Solo Developer Project**
- 15+ years full-stack development experience
- Previous work: VisorPlate e-commerce platform (visorplate-us.com)
- 5 years as a DevSecOps Engineer for UNCOMN
- Tech stack: Laravel, Tailwind, Alpine.js, Livewire, PostgreSQL, AWS

## 📞 Contact & Demo

For demo access or inquiries:
- **GitHub:** [@KoldenAxelson](https://github.com/KoldenAxelson)
- **Live Demo:** *(URL to be provided upon deployment)*

---

**Built with** ❤️ **using the TALL stack (Tailwind, Alpine, Livewire, Laravel)**
