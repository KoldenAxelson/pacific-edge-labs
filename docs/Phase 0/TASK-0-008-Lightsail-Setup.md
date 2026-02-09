# [TASK-0-008] AWS Lightsail Instance Setup & Deployment Script

## Overview
Set up an AWS Lightsail instance for demo deployment and create a deployment script for easy updates. This will be the live demo that Shane/Eldon can test.

## Prerequisites
- [x] AWS CLI configured
- [x] All previous tasks completed (working Laravel app)
- [x] GitHub repository with latest code

## Goals
- Create Lightsail instance ($5/month tier)
- Install PHP 8.2, Nginx, PostgreSQL, Composer
- Configure Lightsail for Laravel
- Create deployment script
- Deploy application to Lightsail
- Configure domain (Lightsail default URL)
- Set up basic monitoring

## Step-by-Step Instructions

### 1. Create Lightsail Instance

```bash
aws lightsail create-instances \
    --instance-names pacific-edge-labs-demo \
    --availability-zone us-west-2a \
    --blueprint-id ubuntu_22_04 \
    --bundle-id micro_2_0 \
    --tags key=Project,value=PacificEdgeLabs key=Environment,value=demo
```

Wait 2-3 minutes for instance to start.

### 2. Allocate Static IP

```bash
aws lightsail allocate-static-ip \
    --static-ip-name pacific-edge-static-ip
```

### 3. Attach Static IP to Instance

```bash
aws lightsail attach-static-ip \
    --static-ip-name pacific-edge-static-ip \
    --instance-name pacific-edge-labs-demo
```

### 4. Get Instance IP Address

```bash
aws lightsail get-static-ip \
    --static-ip-name pacific-edge-static-ip \
    --query 'staticIp.ipAddress' \
    --output text
```

Save this IP address - you'll need it for SSH and DNS.

### 5. Open Firewall Ports

```bash
# HTTP
aws lightsail put-instance-public-ports \
    --instance-name pacific-edge-labs-demo \
    --port-infos fromPort=80,toPort=80,protocol=tcp

# HTTPS (for Phase 7)
aws lightsail put-instance-public-ports \
    --instance-name pacific-edge-labs-demo \
    --port-infos fromPort=443,toPort=443,protocol=tcp

# SSH (already open by default, but verifying)
aws lightsail put-instance-public-ports \
    --instance-name pacific-edge-labs-demo \
    --port-infos fromPort=22,toPort=22,protocol=tcp
```

### 6. Download SSH Key

```bash
aws lightsail download-default-key-pair \
    --region us-west-2 \
    --query 'privateKeyBase64' \
    --output text \
    > ~/.ssh/lightsail-pacific-edge.pem

chmod 400 ~/.ssh/lightsail-pacific-edge.pem
```

### 7. Add SSH Config Entry

Add to `~/.ssh/config`:

```
Host pacific-edge
    HostName <YOUR_STATIC_IP>
    User ubuntu
    IdentityFile ~/.ssh/lightsail-pacific-edge.pem
    ServerAliveInterval 60
```

Replace `<YOUR_STATIC_IP>` with the IP from step 4.

### 8. Test SSH Connection

```bash
ssh pacific-edge
```

You should see the Ubuntu welcome message.

### 9. Install Server Dependencies

While SSH'd into the server:

```bash
# Update system
sudo apt update
sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
    php8.2-xml php8.2-bcmath php8.2-redis php8.2-intl

# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js (for building assets)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Git
sudo apt install -y git unzip

# Install Supervisor (for queue workers - Phase 7)
sudo apt install -y supervisor
```

### 10. Configure PostgreSQL

```bash
# Switch to postgres user
sudo -u postgres psql

# In PostgreSQL prompt:
CREATE DATABASE pacific_edge_labs;
CREATE USER pacific_edge WITH PASSWORD 'CHANGE_THIS_PASSWORD';
GRANT ALL PRIVILEGES ON DATABASE pacific_edge_labs TO pacific_edge;
ALTER DATABASE pacific_edge_labs OWNER TO pacific_edge;
\q

# Exit postgres user
exit
```

**Save the password you used - you'll need it in `.env`**

### 11. Configure PHP

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Find and update these values:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### 12. Create Application Directory

```bash
sudo mkdir -p /var/www/pacific-edge-labs
sudo chown -R ubuntu:www-data /var/www/pacific-edge-labs
cd /var/www/pacific-edge-labs
```

### 13. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/pacific-edge-labs
```

Add this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name <YOUR_STATIC_IP>;
    root /var/www/pacific-edge-labs/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Replace `<YOUR_STATIC_IP>` with your actual IP.

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/pacific-edge-labs /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 14. Create Deployment Script (On Local Machine)

Create `scripts/deploy.sh` in your local project:

```bash
#!/bin/bash

# Pacific Edge Labs - Deployment Script
# Usage: ./scripts/deploy.sh

set -e

echo "🚀 Starting deployment to Lightsail..."

# Configuration
REMOTE_USER="ubuntu"
REMOTE_HOST="pacific-edge"
REMOTE_PATH="/var/www/pacific-edge-labs"
BRANCH="main"

echo "📦 Pushing latest changes to GitHub..."
git push origin $BRANCH

echo "🔗 Connecting to server..."
ssh $REMOTE_HOST << 'ENDSSH'
    set -e
    
    cd /var/www/pacific-edge-labs
    
    echo "📥 Pulling latest code from GitHub..."
    git pull origin main
    
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
    
    echo "📦 Installing NPM dependencies..."
    npm ci
    
    echo "🏗️  Building frontend assets..."
    npm run build
    
    echo "🗄️  Running database migrations..."
    php artisan migrate --force
    
    echo "🧹 Clearing caches..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    
    echo "🔒 Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "🔑 Setting permissions..."
    sudo chown -R ubuntu:www-data /var/www/pacific-edge-labs
    sudo chmod -R 775 /var/www/pacific-edge-labs/storage
    sudo chmod -R 775 /var/www/pacific-edge-labs/bootstrap/cache
    
    echo "✅ Deployment complete!"
ENDSSH

echo "🎉 Deployment successful!"
echo "🌐 Visit: http://YOUR_STATIC_IP"
```

Replace `YOUR_STATIC_IP` at the bottom.

Make it executable:
```bash
chmod +x scripts/deploy.sh
```

### 15. Initial Deployment (Manual First Time)

SSH into the server:
```bash
ssh pacific-edge
cd /var/www/pacific-edge-labs
```

Clone repository:
```bash
git clone https://github.com/YOUR_USERNAME/pacific-edge-labs.git .
```

Replace `YOUR_USERNAME` with your GitHub username.

### 16. Configure Environment (On Server)

```bash
cp .env.example .env
nano .env
```

Update these values:
```env
APP_NAME="Pacific Edge Labs"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_STATIC_IP

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pacific_edge_labs
DB_USERNAME=pacific_edge
DB_PASSWORD=YOUR_POSTGRES_PASSWORD

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password

AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-west-2
AWS_COA_BUCKET=pacific-edge-coas
AWS_PRODUCTS_BUCKET=pacific-edge-products

PAYMENT_GATEWAY=mock
PAYMENT_TEST_MODE=true

SESSION_DRIVER=database
QUEUE_CONNECTION=database

TELESCOPE_ENABLED=false
```

### 17. Generate Application Key

```bash
php artisan key:generate
```

### 18. Run Initial Setup

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Set permissions
sudo chown -R ubuntu:www-data /var/www/pacific-edge-labs
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 19. Test Deployment

Visit: `http://YOUR_STATIC_IP`

You should see the Pacific Edge Labs application!

### 20. Create Admin User on Server

```bash
ssh pacific-edge
cd /var/www/pacific-edge-labs
php artisan make:filament-user
```

Enter:
- Name: Demo Admin
- Email: demo@pacificedgelabs.test
- Password: (choose a strong password)

Then assign super-admin role:
```bash
php artisan tinker
$admin = \App\Models\User::where('email', 'demo@pacificedgelabs.test')->first();
$admin->assignRole('super-admin');
exit
```

### 21. Test Admin Access

Visit: `http://YOUR_STATIC_IP/admin`

Login with the credentials you just created.

### 22. Document Lightsail Resources

Update `docs/aws-resources.md`:

```markdown
# AWS Resources - Pacific Edge Labs

## Lightsail Instance

### pacific-edge-labs-demo
- **Type:** $5/month tier (2GB RAM, 1 CPU, 60GB SSD)
- **Region:** us-west-2a (Oregon)
- **OS:** Ubuntu 22.04 LTS
- **Static IP:** YOUR_STATIC_IP
- **URL:** http://YOUR_STATIC_IP
- **SSH:** `ssh pacific-edge`

**Monthly Cost:** $5.00

### Installed Software
- PHP 8.2 + extensions
- PostgreSQL 14
- Nginx
- Composer
- Node.js 20
- Supervisor

### Deployment
Use `./scripts/deploy.sh` for updates.

## S3 Buckets

### pacific-edge-coas (Private)
- **Purpose:** Certificate of Analysis PDF storage
- **Region:** us-west-2
- **Visibility:** Private (signed URLs only)
- **Versioning:** Enabled
- **Monthly Cost:** ~$0.01

### pacific-edge-products (Public-Read)
- **Purpose:** Product images
- **Region:** us-west-2
- **Visibility:** Public-read
- **Versioning:** Disabled
- **Monthly Cost:** ~$0.05

## Total Monthly Cost
**Lightsail:** $5.00  
**S3:** ~$0.06  
**Total:** ~$5.06/month

## Access

### SSH Access
```bash
ssh pacific-edge
```

### Database Access
```bash
ssh pacific-edge
sudo -u postgres psql pacific_edge_labs
```

### Logs
```bash
# Application logs
ssh pacific-edge
tail -f /var/www/pacific-edge-labs/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

## Maintenance

### Manual Deployment
```bash
./scripts/deploy.sh
```

### Database Backup
```bash
ssh pacific-edge
sudo -u postgres pg_dump pacific_edge_labs > backup.sql
```

### Restart Services
```bash
ssh pacific-edge
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## Monitoring (Phase 7)
- Set up CloudWatch metrics
- Configure uptime monitoring
- Set up error alerting

## Security Hardening (Phase 7)
- Configure UFW firewall
- Install fail2ban
- Set up automatic security updates
- Configure SSL certificate
- Implement rate limiting
```

### 23. Commit Deployment Script

```bash
git add scripts/deploy.sh docs/aws-resources.md
git commit -m "Add Lightsail deployment script and documentation"
git push
```

### 24. Test Deployment Script

From your local machine:

```bash
./scripts/deploy.sh
```

This should:
1. Push to GitHub
2. SSH to server
3. Pull latest code
4. Install dependencies
5. Build assets
6. Run migrations
7. Clear and optimize caches
8. Complete successfully

## Validation Checklist

- [ ] Lightsail instance created and running
- [ ] Static IP allocated and attached
- [ ] SSH access working via `ssh pacific-edge`
- [ ] PHP 8.2 installed and configured
- [ ] PostgreSQL installed and database created
- [ ] Nginx installed and configured
- [ ] Application code deployed
- [ ] http://YOUR_STATIC_IP shows Laravel app
- [ ] http://YOUR_STATIC_IP/admin accessible
- [ ] Can login to admin panel
- [ ] Deployment script runs successfully
- [ ] AWS resources documented

## Common Issues & Solutions

### Issue: "Permission denied" during deployment
**Solution:**
Fix permissions on server:
```bash
ssh pacific-edge
sudo chown -R ubuntu:www-data /var/www/pacific-edge-labs
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: "502 Bad Gateway"
**Solution:**
Check PHP-FPM is running:
```bash
ssh pacific-edge
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

### Issue: Database connection refused
**Solution:**
Verify PostgreSQL is running and credentials are correct:
```bash
ssh pacific-edge
sudo systemctl status postgresql
sudo -u postgres psql -c "\l"
```

### Issue: Assets not loading
**Solution:**
Rebuild assets:
```bash
ssh pacific-edge
cd /var/www/pacific-edge-labs
npm run build
php artisan view:clear
```

### Issue: "APP_KEY not set"
**Solution:**
```bash
ssh pacific-edge
cd /var/www/pacific-edge-labs
php artisan key:generate
php artisan config:cache
```

## Performance Optimization

### Enable OPcache
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
```

### Enable Nginx Gzip
Edit `/etc/nginx/nginx.conf`:
```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
```

### Database Connection Pooling (Phase 7)
Install PgBouncer for connection pooling.

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-008 as complete
- ➡️ Proceed to TASK-0-009 (Database Seeders Framework)

## Time Estimate
**90-120 minutes** (includes waiting for instance creation)

## Success Criteria
- Lightsail instance running and accessible
- Application deployed and working at public URL
- Admin panel accessible
- Deployment script working
- Documentation complete
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** All previous Phase 0 tasks  
**Blocks:** Demo testing by Shane/Eldon  
**Priority:** Critical
