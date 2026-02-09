# [TASK-0-000] Phase 0 Overview: Environment & Foundation

## Purpose
This is a conversational task to plan and generate all Phase 0 tasks with the user. Phase 0 focuses on setting up the development environment, installing core dependencies, and establishing the technical foundation before any feature development begins.

## Phase 0 Goals
- Initialize Laravel 11 project with Sail (Docker)
- Configure PostgreSQL database
- Install authentication (Laravel Breeze)
- Install admin panel (Filament)
- Set up Tailwind CSS with Alpine.js and Livewire
- Configure AWS Lightsail instance for demo deployment
- Set up S3 for file storage (CoA PDFs, product images)
- Install debugging tools (Telescope)
- Establish Git workflow
- Create deployment scripts

## Key Decisions Already Made

### Tech Stack (LOCKED IN)
**Backend:**
- Laravel 11 (latest stable)
- PostgreSQL (database)
- Livewire (reactive components)
- Alpine.js (lightweight JS)

**Frontend:**
- Blade templates
- Tailwind CSS
- Alpine.js for interactions

**Infrastructure:**
- AWS Lightsail ($5/month tier) for demo/initial launch
- AWS S3 for CoA PDFs and product images
- Email abstraction → MailTrap for dev (production mailer TBD)
- Payment abstraction → mock gateway for demo

**Admin:**
- Filament (Laravel admin panel builder)

**Dev Tools:**
- Laravel Sail (Docker-based local dev)
- Laravel Telescope (debugging, dev only)
- Database queues with cron job (no Redis for now)

### Architecture Decisions
- **Single database** for demo (not dual-database like ADUX)
- **Batch-level inventory tracking** (products → batches → CoAs)
- **Account required** for purchases (compliance audit trail)
- **Compliance logging** in dedicated table
- **Payment processor abstraction** (must support swapping gateways)
- **Test as you go** development approach

### Reusable Code
- Can lift ADUX auth structure (Breeze + Alpine pattern)
- VisorPlate patterns available as reference if needed (but building to professional standard)

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Environment Confirmation**
   - "Confirm you're on MacBook Air M2 with Docker Desktop installed?"
   - "Do you have AWS account credentials ready?"
   - "GitHub repo created or should we create it during setup?"

2. **Lightsail Configuration**
   - "What region for Lightsail instance? (US-West-2 for low latency?)"
   - "Domain ready or using Lightsail's default URL for demo?"
   - "Need SSL cert now or defer to Phase 7?"

3. **S3 Bucket Setup**
   - "Create S3 bucket during Phase 0 or when first needed in Phase 3?"
   - "Bucket naming convention? (pacific-edge-coas, pacific-edge-products?)"
   - "Public read for product images, private for CoAs?"

4. **Email Service**
   - "MailTrap for dev is confirmed. What about staging/demo emails?"
   - "Should demo send real emails or just log them?"

5. **Filament Configuration**
   - "Install Filament in Phase 0 or defer to Phase 6?"
   - "Custom Filament theme or use default?"

6. **Git Workflow**
   - "Branch strategy: main only, or main + develop?"
   - "Lightsail deployment from which branch?"

## Suggested Phase 0 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-0-001:** Environment Setup & Project Initialization (Laravel + Sail + Git)
- **TASK-0-002:** Core Dependencies Installation (Breeze, Telescope, Sanctum, Spatie Permissions)
- **TASK-0-003:** Tailwind + Alpine + Livewire Configuration
- **TASK-0-004:** AWS Lightsail Instance Setup & Deployment Script
- **TASK-0-005:** S3 Bucket Creation & Laravel Filesystem Configuration
- **TASK-0-006:** Filament Installation & Basic Configuration
- **TASK-0-007:** Email Abstraction Layer Setup (MailTrap + future swap capability)
- **TASK-0-008:** Payment Abstraction Layer Skeleton (mock gateway)
- **TASK-0-009:** Database Seeders Framework (for demo data in later phases)
- **TASK-0-010:** Testing Setup & First Feature Test

## AI Prompt Template
```
I'm starting Phase 0 of Pacific Edge Labs e-commerce rebuild. This is a peptide research chemical vendor moving from Wix to custom Laravel platform.

Tech stack is locked in:
- Laravel 11 + PostgreSQL
- Blade + Tailwind + Alpine + Livewire (TALL stack)
- Filament for admin
- AWS Lightsail ($5 tier) + S3
- Docker via Laravel Sail for local dev

Phase 0 goals:
1. Get Laravel project running locally via Sail
2. Install core dependencies (Breeze, Filament, Telescope)
3. Configure Lightsail for demo deployment
4. Set up S3 for file storage
5. Create email and payment abstraction layers (mock for now)
6. Establish Git workflow

The demo needs to be live (not local) so Shane/Eldon can test it. We're prioritizing speed - 2 week timeline to working demo.

Key context:
- This is for a family member's business
- Compliance is critical (payment processors are fragile)
- Batch/CoA system is the competitive differentiator
- Must look premium and trustworthy

Let's start by confirming environment setup details...
```

## Important Reminders

### For Environment Setup:
- Sail should use PostgreSQL, Redis, and Meilisearch services
- Configure `.env.example` with all placeholders
- Document every AWS resource created (for cost tracking)
- Keep initial infrastructure lean ($5 Lightsail is sufficient)

### For Dependency Installation:
- Breeze should use Blade stack (not Inertia/Vue)
- Telescope must be dev-only (gate it properly)
- Filament can be installed now or deferred to Phase 6 (user preference)
- All packages should be latest stable versions

### For Abstraction Layers:
- Email abstraction: Interface + MailTrap implementation + placeholder for production
- Payment abstraction: Interface + Mock implementation + placeholder for real gateway
- These enable quick swapping later without touching business logic

### For Deployment:
- Lightsail needs PHP 8.2+, Composer, Nginx, PostgreSQL
- Create deploy script for manual deployment (no CI/CD yet)
- Reserve static IP on Lightsail (domain pointing comes later)
- Document deployment steps clearly

### For Git Workflow:
- `main` branch = production (Phase 8)
- `develop` branch = active development (Phase 0-7)
- Feature branches for individual tasks (optional, user preference)
- Commit messages should reference task numbers

## Success Criteria for Phase 0

At the end of Phase 0, you should have:
- [ ] Laravel project running locally via `./vendor/bin/sail up`
- [ ] Can register/login via Breeze
- [ ] Telescope accessible at `/telescope` (local only)
- [ ] Filament accessible at `/admin` (if installed in Phase 0)
- [ ] Tailwind compiling and hot-reloading working
- [ ] Lightsail instance accessible via SSH
- [ ] Can manually deploy to Lightsail
- [ ] S3 bucket created and configured (if done in Phase 0)
- [ ] Git repo pushed to GitHub
- [ ] Documentation for deployment process
- [ ] `.env.example` with all required variables
- [ ] Database migrations run successfully

**No features built yet** - this is pure infrastructure foundation.

---
**Next Phase:** TASK-1-000 (Design System & Brand Foundation)  
**Phase:** 0 (Environment & Foundation)  
**Approach:** Conversational - discuss, plan, then generate specific tasks  
**Estimated Duration:** 1 day of focused work  
**Priority:** Critical - everything depends on this
