# [TASK-0-001] Environment Setup & Project Initialization

## Overview
Initialize the Laravel 11 project with Sail, configure PostgreSQL, and set up GitHub repository. This is the foundation for all subsequent work.

## Prerequisites
- [x] Mac Mini M2 with Docker Desktop installed and running
- [x] AWS CLI installed and configured
- [x] GitHub account
- [x] ~2TB free disk space

## Goals
- Initialize Laravel 11 project with Sail
- Configure PostgreSQL as primary database
- Create GitHub repository and push initial commit
- Verify local development environment is fully functional

## Step-by-Step Instructions

### 1. Create Project Directory
```bash
cd ~/Developer
curl -s "https://laravel.build/pacific-edge-labs?with=pgsql,redis,meilisearch" | bash
cd pacific-edge-labs
```

**What this does:**
- Creates new Laravel 11 project
- Configures Sail with PostgreSQL, Redis, and Meilisearch services
- Sets up Docker environment automatically

### 2. Start Sail Environment
```bash
./vendor/bin/sail up -d
```

**First run will take 5-10 minutes** as Docker pulls and builds images.

### 3. Verify Services Are Running
```bash
./vendor/bin/sail ps
```

You should see:
- `pacific-edge-labs-laravel.test-1` (running)
- `pacific-edge-labs-pgsql-1` (running)
- `pacific-edge-labs-redis-1` (running)
- `pacific-edge-labs-meilisearch-1` (running)

### 4. Create Sail Alias (Quality of Life)
Add to `~/.zshrc`:
```bash
alias sail='./vendor/bin/sail'
```

Then:
```bash
source ~/.zshrc
```

Now you can use `sail` instead of `./vendor/bin/sail` 🎉

### 5. Configure Environment Variables
```bash
sail artisan env
```

Verify your `.env` file has:
```env
APP_NAME="Pacific Edge Labs"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=pacific_edge_labs
DB_USERNAME=sail
DB_PASSWORD=password

# These will be configured in later tasks
MAIL_MAILER=log
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-west-2
```

### 6. Run Initial Migrations
```bash
sail artisan migrate
```

You should see:
```
✓ 0001_01_01_000000_create_users_table
✓ 0001_01_01_000001_create_cache_table
✓ 0001_01_01_000002_create_jobs_table
```

### 7. Test Application in Browser
Open: http://localhost

You should see the Laravel welcome page.

### 8. Create GitHub Repository

**Via GitHub CLI (if installed):**
```bash
gh repo create pacific-edge-labs --public --source=. --remote=origin
```

**Or manually:**
1. Go to https://github.com/new
2. Repository name: `pacific-edge-labs`
3. Visibility: **Public**
4. **Do NOT** initialize with README (we already have a Laravel project)
5. Click "Create repository"

### 9. Initialize Git and Push
```bash
git init
git add .
git commit -m "Initial Laravel 11 project with Sail (PostgreSQL, Redis, Meilisearch)"
git branch -M main
git remote add origin git@github.com:YOUR_USERNAME/pacific-edge-labs.git
git push -u origin main
```

Replace `YOUR_USERNAME` with your actual GitHub username.

### 10. Add License File
Since this is a public repo, add an MIT License:

```bash
curl -o LICENSE https://raw.githubusercontent.com/licenses/license-templates/master/templates/mit.txt
```

Edit the LICENSE file to add your name and year (2025), then:
```bash
git add LICENSE
git commit -m "Add MIT License"
git push
```

### 11. Create README.md
```bash
cat > README.md << 'EOF'
# Pacific Edge Labs E-Commerce Platform

Premium peptide research chemical vendor platform built with Laravel 11.

## Tech Stack
- **Backend:** Laravel 11, PostgreSQL, Livewire
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Admin:** Filament
- **Infrastructure:** AWS Lightsail, S3
- **Development:** Laravel Sail (Docker)

## Local Development Setup

### Prerequisites
- Docker Desktop
- PHP 8.2+ (for Composer)
- Git

### Installation
```bash
git clone git@github.com:YOUR_USERNAME/pacific-edge-labs.git
cd pacific-edge-labs
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

Access the application at http://localhost

### Useful Commands
```bash
sail up -d          # Start Docker containers
sail down           # Stop Docker containers
sail artisan        # Run Artisan commands
sail composer       # Run Composer commands
sail npm            # Run NPM commands
sail test           # Run PHPUnit tests
```

## Project Phases
- **Phase 0:** Environment & Foundation ✅
- **Phase 1:** Design System & Brand Foundation (In Progress)
- **Phase 2:** Product Catalog & Batch System
- **Phase 3:** Certificate of Analysis (CoA) System
- **Phase 4:** Shopping Cart & Checkout
- **Phase 5:** Customer Accounts & Order Management
- **Phase 6:** Admin Dashboard (Filament)
- **Phase 7:** Compliance & Security Hardening
- **Phase 8:** Production Deployment & Handoff

## License
MIT License - see LICENSE file for details

## Development Notes
This project prioritizes compliance, security, and premium user experience for peptide research chemical e-commerce.
EOF
```

Replace `YOUR_USERNAME`, then:
```bash
git add README.md
git commit -m "Add comprehensive README"
git push
```

## Validation Checklist

Run through this checklist to verify everything is working:

- [ ] `sail ps` shows all 4 containers running
- [ ] http://localhost displays Laravel welcome page
- [ ] `sail artisan migrate:status` shows migrations installed
- [ ] `sail artisan tinker` opens successfully (type `exit` to close)
- [ ] GitHub repository exists and is public
- [ ] Local git repo is connected to GitHub (`git remote -v`)
- [ ] Latest code is pushed to GitHub
- [ ] LICENSE and README.md files are present
- [ ] `.env` file is configured (NOT committed to Git)
- [ ] `sail` alias works in terminal

## Common Issues & Solutions

### Issue: "Port already in use"
**Solution:**
```bash
sail down
lsof -ti:80 | xargs kill -9  # Kill process using port 80
sail up -d
```

### Issue: "Database connection refused"
**Solution:**
```bash
sail down -v  # Remove volumes
sail up -d
sail artisan migrate
```

### Issue: Slow Docker performance on M2
**Solution:**
- Ensure Docker Desktop is using Apple Silicon native version
- Increase Docker memory allocation to 4GB+ in Docker Desktop settings

### Issue: Permission errors
**Solution:**
```bash
sudo chown -R $USER:$USER ~/Developer/pacific-edge-labs
```

## Environment Variables Reference

These are set in `.env` and should **never** be committed:

```env
# Application
APP_NAME="Pacific Edge Labs"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database (Sail defaults)
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=pacific_edge_labs
DB_USERNAME=sail
DB_PASSWORD=password

# Mail (configured in TASK-0-006)
MAIL_MAILER=smtp
MAILTRAP_API_KEY=

# AWS (configured in TASK-0-004)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=

# Redis (Sail defaults)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Meilisearch (Sail defaults)
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=
```

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-001 as complete
- ➡️ Proceed to TASK-0-002 (Core Dependencies Installation)

## Time Estimate
**30-45 minutes** (including Docker image downloads on first run)

## Success Criteria
- Laravel application running at http://localhost
- PostgreSQL database accessible and migrations installed
- GitHub repository created with initial code pushed
- Development environment fully functional
- README and LICENSE files present

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** None (first task)  
**Blocks:** TASK-0-002, TASK-0-003, all subsequent tasks  
**Priority:** Critical
