# [INFO-0-008] AWS Lightsail Instance Setup & Deployment - Completion Report

## Metadata
- **Task:** TASK-0-008-Lightsail-Setup
- **Phase:** 0 (Environment & Foundation)
- **Completed:** 2025-02-10
- **Duration:** ~2.5 hours
- **Status:** ✅ Complete

## What We Did
Successfully deployed Pacific Edge Labs to AWS Lightsail with full production environment setup and one-command deployment workflow.

- Created Lightsail instance (micro_2_0 tier, Ubuntu 22.04)
- Allocated and attached static IP (100.23.227.53)
- Installed PHP 8.5.2, PostgreSQL 18.1, Node 20, Nginx, Composer
- Configured PostgreSQL database and user
- Configured Nginx for Laravel application
- Cloned repository and deployed application
- Created deployment automation scripts
- Fixed Filament production authorization
- Verified admin panel and public site working

## Deviations from Plan

**Deviation 1: SSH Key Format Issues**
- **Issue:** Default Lightsail key pair download didn't work with standard SSH
- **Solution:** Used existing SSH key (id_ed25519) instead of Lightsail default
- **Impact:** Simpler SSH setup, reused existing key
- **Files affected:** None, just skipped default key setup

**Deviation 2: Telescope Production Conflict**
- **Issue:** TelescopeServiceProvider tried to load in production without dev dependencies
- **Solution:** Made Telescope conditional in bootstrap/providers.php
- **Impact:** Clean production deploys without dev packages
- **Files affected:** `bootstrap/providers.php`

**Deviation 3: Filament Authorization 403**
- **Issue:** Admin panel showed 403 after login due to missing FilamentUser interface
- **Solution:** Implemented FilamentUser contract in User model
- **Impact:** Required for Filament 3+ production authorization
- **Files affected:** `app/Models/User.php`

**Deviation 4: Version Matching**
- **Plan suggested:** PHP 8.2, PostgreSQL 14
- **Actually installed:** PHP 8.5.2, PostgreSQL 18.1 (matched local dev exactly)
- **Impact:** Perfect local/production parity
- **Reason:** Available in repositories, better to match local

## Confirmed Working

- ✅ **Lightsail instance:** Running and accessible at 100.23.227.53
- ✅ **Static IP:** Attached and persistent
- ✅ **SSH access:** Working via `ssh pacific-edge`
- ✅ **PHP 8.5.2:** Installed with all required extensions
- ✅ **PostgreSQL 18.1:** Running with pacific_edge_labs database
- ✅ **Nginx:** Configured and serving Laravel app
- ✅ **Node 20.20.0:** Building assets successfully
- ✅ **Composer 2.9.5:** Installing dependencies
- ✅ **Application deployed:** http://100.23.227.53 loads homepage
- ✅ **Admin panel:** http://100.23.227.53/admin accessible and working
- ✅ **Authentication:** Login/logout functioning
- ✅ **Filament Shield:** Roles and permissions working
- ✅ **Assets building:** Vite compiling CSS/JS properly
- ✅ **Database migrations:** All tables created
- ✅ **Deployment script:** One-command deploy working
- ✅ **Local helper script:** `.bin/deploy_pacific_edge.sh` automated

## Important Notes

**Server Specifications**
- **Instance Type:** micro_2_0 ($5/month)
- **RAM:** 1GB
- **CPU:** 1 vCPU
- **Storage:** 40GB SSD
- **Region:** us-west-2a (Oregon)
- **OS:** Ubuntu 22.04 LTS
- **Static IP:** 100.23.227.53

**Installed Software Versions**
- PHP: 8.5.2 (matches local)
- PostgreSQL: 18.1 (matches local)
- Node.js: 20.20.0 (LTS, close to local 24.13)
- Nginx: 1.18.0
- Composer: 2.9.5

**Database Configuration**
- Database name: `pacific_edge_labs`
- Username: `pacific_edge`
- Password: `PacificBadDatabase` (demo only, change for production)
- Host: 127.0.0.1
- Port: 5432

**PHP Configuration Changes**
- upload_max_filesize: 10M
- post_max_size: 10M
- max_execution_time: 300
- memory_limit: 256M

**Nginx Configuration**
- Server name: 100.23.227.53
- Document root: /var/www/pacific-edge-labs/public
- PHP-FPM: unix socket at /var/run/php/php8.5-fpm.sock
- FastCGI configured for Laravel

**Deployment Workflow**
1. Local: Make code changes
2. Local: Commit and push to GitHub
3. Local: Run `.bin/deploy_pacific_edge.sh`
4. Remote: Automatically pulls, builds, migrates, optimizes
5. Result: Live site updated at http://100.23.227.53

**Filament Production Fix**
- User model must implement `Filament\Models\Contracts\FilamentUser`
- Method `canAccessPanel(Panel $panel)` required
- Without interface: 403 forbidden error in production
- Local environments don't enforce this (debug mode bypass)

**Telescope Production Fix**
- Telescope is dev-only dependency
- Must be conditionally loaded in bootstrap/providers.php
- Production deployments use `--no-dev` flag
- Conditional: `...(app()->environment('local') ? [TelescopeServiceProvider::class] : [])`

## Blockers Encountered

**Blocker #1: SSH Key Authentication**
- **Description:** Default Lightsail key pair download failed to work with SSH
- **Error:** `Load key: invalid format`
- **Resolution:** Used browser SSH console to add local SSH public key to authorized_keys
- **Time lost:** ~20 minutes

**Blocker #2: Filament 403 After Login**
- **Description:** Admin panel accessible locally but 403 in production
- **Investigation:** Checked roles, permissions, canAccessPanel - all returned true
- **Resolution:** Had to implement FilamentUser interface, not just method
- **Time lost:** ~30 minutes
- **Root cause:** Filament 3+ requires contract implementation for production

**Blocker #3: Telescope Class Not Found**
- **Description:** Deployment failed with TelescopeApplicationServiceProvider error
- **Error:** Class not found because Telescope removed with `--no-dev`
- **Resolution:** Made service provider conditional on environment
- **Time lost:** ~10 minutes

## Configuration Changes
```
File: bootstrap/providers.php
Changes: Made Telescope conditional for production
  - Wrapped TelescopeServiceProvider in environment check
  - Only loads in 'local' environment now
```
```
File: app/Models/User.php
Changes: Implemented FilamentUser interface
  - Added use statement for FilamentUser contract
  - Added implements FilamentUser to class declaration
  - Method canAccessPanel() already existed
```
```
File: .bin/deploy_pacific_edge.sh (new)
Changes: Created local deployment helper
  - Connects via SSH to Lightsail
  - Executes remote deploy.sh script
  - Shows deployment status
```
```
File: /var/www/pacific-edge-labs/deploy.sh (remote, new)
Changes: Created server-side deployment automation
  - Git pull
  - Composer install (production mode)
  - NPM build
  - Database migrations
  - Cache optimization
  - Permission setting
```
```
File: ~/.ssh/config (local)
Changes: Added SSH alias for Lightsail
  - Host: pacific-edge
  - HostName: 100.23.227.53
  - User: ubuntu
  - IdentityFile: ~/.ssh/id_ed25519
```
```
File: /etc/nginx/sites-available/pacific-edge-labs (remote)
Changes: Created Nginx virtual host
  - Server name: 100.23.227.53
  - Root: /var/www/pacific-edge-labs/public
  - PHP-FPM socket: php8.5-fpm.sock
  - Laravel rewrite rules
```
```
File: /etc/php/8.5/fpm/php.ini (remote)
Changes: Increased PHP limits
  - upload_max_filesize = 10M
  - post_max_size = 10M
  - max_execution_time = 300
  - memory_limit = 256M
```
```
File: /var/www/pacific-edge-labs/.env (remote)
Changes: Production environment configuration
  - APP_ENV=production
  - APP_DEBUG=false
  - APP_URL=http://100.23.227.53
  - DB_* configured for local PostgreSQL
  - TELESCOPE_ENABLED=false
  - PAYMENT_GATEWAY=mock
```

## Next Steps

TASK-0-008 is complete. Phase 0 continues with:

- **TASK-0-009:** Database Seeders Framework
  - Product catalog seeder
  - User roles seeder
  - Sample data seeder
  - Estimated time: ~60 minutes

- **Future Lightsail Enhancements (Phase 7):**
  - SSL certificate (Let's Encrypt)
  - Domain name configuration
  - CloudWatch monitoring
  - Automated backups
  - UFW firewall configuration
  - Fail2ban for SSH protection
  - Queue worker with Supervisor
  - Production payment gateway

- **Cost Monitoring:**
  - Lightsail: $5.00/month
  - S3 storage: ~$0.06/month
  - Total: ~$5.06/month for demo

## Files Created/Modified

**Local Machine:**
- `.bin/deploy_pacific_edge.sh` - deployment helper script
- `~/.ssh/config` - SSH alias for Lightsail
- `bootstrap/providers.php` - conditional Telescope loading
- `app/Models/User.php` - FilamentUser interface implementation

**Lightsail Server:**
- `/var/www/pacific-edge-labs/deploy.sh` - deployment automation
- `/etc/nginx/sites-available/pacific-edge-labs` - Nginx config
- `/etc/php/8.5/fpm/php.ini` - PHP configuration
- `/var/www/pacific-edge-labs/.env` - production environment

**Total Changes:** 2 modified PHP files, 2 new scripts, 2 configuration files

---

## For Next Claude

**Environment Context:**
- Lightsail instance fully operational at 100.23.227.53
- Production deployment workflow established
- Admin panel accessible and working
- All Phase 0 infrastructure complete

**Lightsail Access:**
```bash
# SSH into server
ssh pacific-edge
# or
ssh -i ~/.ssh/id_ed25519 ubuntu@100.23.227.53

# Deploy updates
.bin/deploy_pacific_edge.sh
```

**Server Details:**
- **IP:** 100.23.227.53
- **SSH User:** ubuntu
- **App Path:** /var/www/pacific-edge-labs
- **Database:** pacific_edge_labs
- **DB User:** pacific_edge
- **DB Password:** PacificBadDatabase

**URLs:**
- **Homepage:** http://100.23.227.53
- **Admin:** http://100.23.227.53/admin
- **Test Payment:** http://100.23.227.53/test-payment

**Admin Credentials:**
- **Email:** KonradWright@Protonmail.com
- **Role:** super-admin
- **Password:** [user knows password]

**Deployment Workflow:**
```bash
# 1. Make changes locally
# 2. Commit and push
git add .
git commit -m "Your changes"
git push

# 3. Deploy to Lightsail
.bin/deploy_pacific_edge.sh

# Script automatically:
# - Pulls latest code
# - Installs dependencies
# - Builds assets
# - Runs migrations
# - Clears/caches config
# - Sets permissions
```

**Manual Server Operations:**
```bash
# SSH into server
ssh pacific-edge

# View application logs
tail -f /var/www/pacific-edge-labs/storage/logs/laravel.log

# View Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Restart services
sudo systemctl restart php8.5-fpm
sudo systemctl restart nginx

# Database access
sudo -u postgres psql pacific_edge_labs

# Manual deployment (if script fails)
cd /var/www/pacific-edge-labs
./deploy.sh
```

**Critical Production Fixes Applied:**
1. **FilamentUser Interface:** User model must implement `Filament\Models\Contracts\FilamentUser` - without this, production admin panel returns 403
2. **Telescope Conditional:** Service provider must be environment-conditional - otherwise `composer install --no-dev` breaks
3. **SSH Key:** Using id_ed25519 instead of Lightsail default key

**Known Working Features:**
- ✅ Homepage loads
- ✅ Admin panel accessible
- ✅ Login/logout working
- ✅ Filament resources displaying
- ✅ Database connected
- ✅ Assets building and loading
- ✅ Payment service (mock mode)
- ✅ Roles and permissions
- ✅ Deployment automation

**Known Issues:**
- None! Everything working as expected

**Phase 0 Status:**
- ✅ TASK-0-001: Laravel initialization
- ✅ TASK-0-002: Database setup
- ✅ TASK-0-003: Authentication
- ✅ TASK-0-004: Admin panel (Filament)
- ✅ TASK-0-005: Permissions
- ✅ TASK-0-006: AWS S3 setup
- ✅ TASK-0-007: Payment abstraction
- ✅ TASK-0-008: Lightsail deployment
- ⏳ TASK-0-009: Database seeders (next)

**Ready for Next Task:**
- Infrastructure complete and tested
- Deployment workflow automated
- Demo environment live and accessible
- Can proceed with database seeders

**Git Status:**
- All Lightsail configuration committed
- Deployment scripts committed
- FilamentUser fix committed
- Telescope fix committed
- Repository clean and up to date

**Monthly Costs:**
- Lightsail instance: $5.00
- S3 storage (CoAs): ~$0.03
- S3 storage (products): ~$0.03
- **Total:** ~$5.06/month

**Performance Notes:**
- 1GB RAM sufficient for demo with light traffic
- PHP 8.5 OPcache enabled
- Nginx gzip compression enabled
- Asset building takes ~10 seconds
- Page loads < 1 second

**Security Notes:**
- SSH key authentication only (no passwords)
- Firewall: ports 22, 80, 443 only
- APP_DEBUG=false in production
- Database password should be changed for real production
- SSL not yet configured (Phase 7)

**Next Phase Preview:**
After TASK-0-009 (Seeders), Phase 1 begins:
- Design system implementation
- Brand colors and typography
- Reusable Blade components
- Homepage design

Demo is ready for Shane/Eldon testing once Phase 1-2 complete (public product pages).
