# [TASK-0-002] Core Dependencies Installation

## Overview
Install and configure core Laravel packages that form the foundation of the application: Breeze (authentication), Telescope (debugging), Sanctum (API tokens), and Spatie Permissions (role management).

## Prerequisites
- [x] TASK-0-001 completed (Laravel project initialized and running)
- [x] Sail environment running (`sail ps` shows all services)

## Goals
- Install Laravel Breeze with Blade stack for authentication
- Install Laravel Telescope for debugging (dev only)
- Install Laravel Sanctum for API token management
- Install Spatie Laravel Permission for roles & permissions
- Configure all packages for immediate use

## Step-by-Step Instructions

### 1. Install Laravel Breeze (Authentication)

```bash
sail composer require laravel/breeze --dev
sail artisan breeze:install blade
```

**When prompted, select:**
- Which Breeze stack would you like to install? → **blade**
- Would you like dark mode support? → **yes**
- Which testing framework do you prefer? → **PHPUnit**

This installs:
- Login/Register/Password Reset views
- Authentication routes
- Tailwind CSS (configured)
- Alpine.js
- Profile management

### 2. Run Breeze Migrations
```bash
sail artisan migrate
```

### 3. Install NPM Dependencies
```bash
sail npm install
sail npm run dev
```

**Keep this terminal open** - Vite dev server needs to run for asset compilation.

Open a **new terminal** for remaining commands.

### 4. Test Authentication in Browser

Visit: http://localhost/register

Create a test account:
- Name: Test User
- Email: test@example.com
- Password: password

You should be redirected to the dashboard after registration.

### 5. Install Laravel Telescope (Debugging)

```bash
sail composer require laravel/telescope --dev
sail artisan telescope:install
sail artisan migrate
```

### 6. Configure Telescope for Local Only

Edit `app/Providers/TelescopeServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        // Only register Telescope in local environment
        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'test@example.com', // Add your email here
            ]);
        });
    }
}
```

### 7. Test Telescope Access

Visit: http://localhost/telescope

You should see the Telescope dashboard with recent requests, queries, etc.

### 8. Install Laravel Sanctum (API Tokens)

```bash
sail artisan install:api
```

This publishes Sanctum configuration and migrations.

```bash
sail artisan migrate
```

### 9. Install Spatie Laravel Permission

```bash
sail composer require spatie/laravel-permission
sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
sail artisan migrate
```

### 10. Configure Spatie Permission Model

Edit `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### 11. Create Basic Roles Seeder

```bash
sail artisan make:seeder RoleSeeder
```

Edit `database/seeders/RoleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);

        // Create basic permissions (will expand in later phases)
        Permission::create(['name' => 'view admin panel']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage orders']);
        Permission::create(['name' => 'manage users']);

        // Assign permissions to roles
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'view admin panel',
            'manage products',
            'manage orders',
        ]);
    }
}
```

### 12. Update DatabaseSeeder

Edit `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RoleSeeder first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create test users
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@pacificedgelabs.test',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('super-admin');

        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@pacificedgelabs.test',
            'password' => bcrypt('password'),
        ]);
        $customer->assignRole('customer');
    }
}
```

### 13. Run Seeders
```bash
sail artisan db:seed
```

### 14. Test Role Assignment

```bash
sail artisan tinker
```

In Tinker:
```php
$user = User::where('email', 'admin@pacificedgelabs.test')->first();
$user->hasRole('super-admin'); // Should return true
$user->can('manage products'); // Should return true
exit
```

### 15. Update `.env.example`

This file serves as a template for other developers. Add any new environment variables:

```bash
cp .env .env.example
```

Then edit `.env.example` and **remove sensitive values**:

```env
APP_NAME="Pacific Edge Labs"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=pacific_edge_labs
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=

TELESCOPE_ENABLED=true
```

### 16. Commit Changes

```bash
git add .
git commit -m "Install core dependencies: Breeze, Telescope, Sanctum, Spatie Permissions"
git push
```

## Validation Checklist

- [ ] Can register new user at http://localhost/register
- [ ] Can login at http://localhost/login
- [ ] Can logout successfully
- [ ] Can reset password (check logs for reset link)
- [ ] Dashboard accessible after login
- [ ] Telescope accessible at http://localhost/telescope
- [ ] Telescope shows recent queries and requests
- [ ] `sail artisan tinker` → User model has `hasRole()` method
- [ ] Seeders create super-admin and customer users
- [ ] `php artisan route:list` shows Breeze auth routes
- [ ] NPM dev server running and compiling assets
- [ ] No console errors in browser (check browser dev tools)

## Common Issues & Solutions

### Issue: "Vite manifest not found"
**Solution:**
```bash
sail npm install
sail npm run dev
```
Keep the dev server running in a separate terminal.

### Issue: "Class 'Spatie\Permission\Models\Role' not found"
**Solution:**
```bash
sail artisan config:clear
sail artisan cache:clear
sail composer dump-autoload
```

### Issue: Telescope shows "Unable to find a user to log"
**Solution:**
This is normal if you haven't created any users yet. Register a user and Telescope will start tracking.

### Issue: Permission errors in Tinker
**Solution:**
Make sure seeders have run:
```bash
sail artisan migrate:fresh --seed
```

## Package Reference

### Laravel Breeze
- **Purpose:** Full authentication scaffolding
- **Routes:** `/login`, `/register`, `/forgot-password`, `/reset-password`, `/dashboard`
- **Views:** `resources/views/auth/*`, `resources/views/profile/*`
- **Controllers:** `app/Http/Controllers/Auth/*`, `app/Http/Controllers/ProfileController.php`

### Laravel Telescope
- **Purpose:** Debugging and monitoring
- **Access:** http://localhost/telescope (local only)
- **Features:** Request tracking, query logging, exception monitoring, job tracking
- **Storage:** Stored in database (can be pruned)

### Laravel Sanctum
- **Purpose:** API token authentication (for future mobile app or API access)
- **Tables:** `personal_access_tokens`
- **Usage:** Will be used in later phases for checkout API

### Spatie Laravel Permission
- **Purpose:** Role and permission management
- **Tables:** `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`
- **Usage:** Will be heavily used in Phase 6 (Admin) and Phase 7 (Compliance)

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-002 as complete
- ➡️ Proceed to TASK-0-003 (Tailwind + Alpine + Livewire Configuration)

## Time Estimate
**45-60 minutes**

## Success Criteria
- Full authentication system working (register, login, logout, password reset)
- Telescope accessible and tracking requests
- Sanctum installed and configured
- Spatie Permission installed with basic roles seeded
- Test users created with roles assigned
- All changes committed and pushed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001  
**Blocks:** TASK-0-003, TASK-0-005 (Filament needs auth)  
**Priority:** Critical
