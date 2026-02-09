# [TASK-0-009] Database Seeders Framework

## Overview
Create a comprehensive database seeding framework to generate realistic demo data for testing. This will populate products, batches, CoAs, orders, and customers for the demo deployment.

## Prerequisites
- [x] TASK-0-001 through TASK-0-008 completed
- [x] User and Role models exist
- [x] Application deployed to Lightsail (or ready for Phase 1)

## Goals
- Create seeders for all models (some will be placeholder for future phases)
- Generate realistic test data
- Make seeders idempotent (can run multiple times)
- Create separate seeders for development vs. production demo
- Document seeder usage

## Important Note

Some models don't exist yet (Product, Batch, Order, etc.) - that's fine! We'll create placeholder seeders now that will be expanded in later phases. This task establishes the framework.

## Step-by-Step Instructions

### 1. Create Base Seeder Trait

Create `app/Traits/SeederHelpers.php`:

```php
<?php

namespace App\Traits;

trait SeederHelpers
{
    /**
     * Truncate tables and reset auto-increment
     */
    protected function truncateTable(string $table): void
    {
        \DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
    }

    /**
     * Check if seeder should run (useful for production safety)
     */
    protected function shouldSeed(string $environment = 'local'): bool
    {
        return app()->environment($environment);
    }

    /**
     * Display progress message
     */
    protected function info(string $message): void
    {
        if (method_exists($this, 'command')) {
            $this->command->info($message);
        }
    }

    /**
     * Generate random date within range
     */
    protected function randomDateBetween(string $startDate, string $endDate): \DateTime
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $randomTimestamp = mt_rand($start, $end);
        
        return new \DateTime('@' . $randomTimestamp);
    }
}
```

### 2. Update Existing RoleSeeder

We already have this from TASK-0-002, but let's enhance it:

Edit `database/seeders/RoleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('Seeding roles and permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete existing roles and permissions (for fresh seeding)
        Permission::query()->delete();
        Role::query()->delete();

        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $customer = Role::create(['name' => 'customer']);

        // Create permissions
        $permissions = [
            // Admin panel
            'view admin panel',
            
            // User management
            'manage users',
            'view users',
            
            // Product management
            'manage products',
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Batch management
            'manage batches',
            'view batches',
            'create batches',
            'edit batches',
            'delete batches',
            
            // CoA management
            'manage coas',
            'view coas',
            'upload coas',
            'delete coas',
            
            // Order management
            'manage orders',
            'view orders',
            'edit orders',
            'cancel orders',
            'refund orders',
            
            // Customer management
            'view customers',
            'edit customers',
            
            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to super-admin
        $superAdmin->givePermissionTo(Permission::all());

        // Assign selected permissions to admin
        $admin->givePermissionTo([
            'view admin panel',
            'manage products',
            'view products',
            'create products',
            'edit products',
            'manage batches',
            'view batches',
            'create batches',
            'edit batches',
            'manage coas',
            'view coas',
            'upload coas',
            'manage orders',
            'view orders',
            'edit orders',
            'view customers',
        ]);

        // Customer role has no admin permissions
        $this->info('✓ Roles and permissions created');
    }
}
```

### 3. Create UserSeeder

```bash
sail artisan make:seeder UserSeeder
```

Edit `database/seeders/UserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('Seeding users...');

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pacificedgelabs.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');
        $this->info('✓ Created super admin');

        // Create regular admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'staff@pacificedgelabs.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $this->info('✓ Created admin user');

        // Create test customers
        $customerNames = [
            'Dr. Sarah Johnson',
            'Prof. Michael Chen',
            'Dr. Emily Rodriguez',
            'Dr. James Anderson',
            'Dr. Lisa Thompson',
            'Prof. David Kim',
            'Dr. Rachel Martinez',
            'Dr. Robert Taylor',
            'Dr. Jennifer Lee',
            'Prof. Christopher Brown',
        ];

        foreach ($customerNames as $index => $name) {
            $customer = User::create([
                'name' => $name,
                'email' => strtolower(str_replace([' ', '.'], ['', ''], explode(' ', $name)[1])) . ($index + 1) . '@research.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $customer->assignRole('customer');
        }
        
        $this->info('✓ Created 10 test customers');
    }
}
```

### 4. Create Placeholder ProductSeeder

```bash
sail artisan make:seeder ProductSeeder
```

Edit `database/seeders/ProductSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('ProductSeeder - Will be implemented in Phase 2');
        
        // Placeholder - Product model will be created in Phase 2
        // This seeder will create products like:
        // - BPC-157
        // - TB-500
        // - Ipamorelin
        // - Sermorelin
        // - etc.
        
        $this->info('✓ Product seeder ready for Phase 2');
    }
}
```

### 5. Create Placeholder BatchSeeder

```bash
sail artisan make:seeder BatchSeeder
```

Edit `database/seeders/BatchSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('BatchSeeder - Will be implemented in Phase 2');
        
        // Placeholder - Batch model will be created in Phase 2
        // This seeder will create batches for products with:
        // - Batch numbers
        // - Quantities
        // - Manufacturing dates
        // - Expiry dates
        
        $this->info('✓ Batch seeder ready for Phase 2');
    }
}
```

### 6. Create Placeholder CoaSeeder

```bash
sail artisan make:seeder CoaSeeder
```

Edit `database/seeders/CoaSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('CoaSeeder - Will be implemented in Phase 3');
        
        // Placeholder - CoA model will be created in Phase 3
        // This seeder will:
        // - Generate sample PDF CoAs
        // - Upload to S3
        // - Link to batches
        
        $this->info('✓ CoA seeder ready for Phase 3');
    }
}
```

### 7. Create Placeholder OrderSeeder

```bash
sail artisan make:seeder OrderSeeder
```

Edit `database/seeders/OrderSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use SeederHelpers;

    public function run(): void
    {
        $this->info('OrderSeeder - Will be implemented in Phase 4');
        
        // Placeholder - Order model will be created in Phase 4
        // This seeder will create realistic orders with:
        // - Multiple products per order
        // - Various statuses (pending, processing, shipped, delivered)
        // - Payment transactions
        // - Order history over past 6 months
        
        $this->info('✓ Order seeder ready for Phase 4');
    }
}
```

### 8. Update DatabaseSeeder

Edit `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        
        $this->call([
            // Phase 0: Foundation
            RoleSeeder::class,
            UserSeeder::class,
            
            // Phase 2: Products & Inventory (placeholder for now)
            ProductSeeder::class,
            BatchSeeder::class,
            
            // Phase 3: CoAs (placeholder for now)
            CoaSeeder::class,
            
            // Phase 4: Orders (placeholder for now)
            OrderSeeder::class,
        ]);
        
        $this->command->info('✅ Database seeding completed!');
        $this->command->newLine();
        $this->command->info('Test accounts created:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@pacificedgelabs.test', 'password'],
                ['Admin', 'staff@pacificedgelabs.test', 'password'],
                ['Customer', 'johnson1@research.test', 'password'],
            ]
        );
    }
}
```

### 9. Create Development Seeder (Extra Verbose Data)

```bash
sail artisan make:seeder DevelopmentSeeder
```

Edit `database/seeders/DevelopmentSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run development-specific seeders
     * This creates extra verbose data for local testing
     */
    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command->error('DevelopmentSeeder should only run in local environment!');
            return;
        }

        $this->info('Running development seeders...');

        // Create extra test users
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => "Test Customer {$i}",
                'email' => "customer{$i}@test.local",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ])->assignRole('customer');
        }

        $this->info('✓ Created 50 additional test customers');
        
        // Future: Create lots of orders, products, etc. for stress testing
    }
}
```

### 10. Create Production Demo Seeder

```bash
sail artisan make:seeder ProductionDemoSeeder
```

Edit `database/seeders/ProductionDemoSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionDemoSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Run production demo seeder
     * This creates realistic but limited data for demo deployment
     */
    public function run(): void
    {
        $this->info('Running production demo seeder...');

        // Only create demo admin if it doesn't exist
        if (!User::where('email', 'demo@pacificedgelabs.test')->exists()) {
            $demoAdmin = User::create([
                'name' => 'Demo Admin',
                'email' => 'demo@pacificedgelabs.test',
                'password' => Hash::make(env('DEMO_ADMIN_PASSWORD', 'change-this-password')),
                'email_verified_at' => now(),
            ]);
            $demoAdmin->assignRole('super-admin');
            
            $this->info('✓ Created demo admin account');
        } else {
            $this->info('✓ Demo admin already exists');
        }

        // Create a few realistic demo customers
        $demoCustomers = [
            ['name' => 'Dr. Alex Morgan', 'email' => 'alex.morgan@university.edu'],
            ['name' => 'Dr. Sam Rivera', 'email' => 'sam.rivera@research.org'],
            ['name' => 'Dr. Jordan Lee', 'email' => 'jordan.lee@biotech.com'],
        ];

        foreach ($demoCustomers as $customerData) {
            if (!User::where('email', $customerData['email'])->exists()) {
                User::create([
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'password' => Hash::make('demo-password'),
                    'email_verified_at' => now(),
                ])->assignRole('customer');
            }
        }

        $this->info('✓ Created 3 demo customers');
    }
}
```

### 11. Create Artisan Commands for Easy Seeding

```bash
sail artisan make:command SeedDevelopment
sail artisan make:command SeedDemo
```

Edit `app/Console/Commands/SeedDevelopment.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDevelopment extends Command
{
    protected $signature = 'db:seed-dev';
    protected $description = 'Seed database with development data';

    public function handle()
    {
        if (!app()->environment('local')) {
            $this->error('This command should only run in local environment!');
            return 1;
        }

        $this->info('Seeding development database...');
        
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DevelopmentSeeder']);
        
        $this->info('✅ Development database seeded!');
        return 0;
    }
}
```

Edit `app/Console/Commands/SeedDemo.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDemo extends Command
{
    protected $signature = 'db:seed-demo';
    protected $description = 'Seed database with production demo data';

    public function handle()
    {
        $this->info('Seeding demo database...');
        
        if ($this->confirm('This will WIPE the database and reseed. Continue?')) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ProductionDemoSeeder']);
            
            $this->info('✅ Demo database seeded!');
        } else {
            $this->info('Cancelled.');
        }
        
        return 0;
    }
}
```

### 12. Test Seeders Locally

```bash
# Test basic seeding
sail artisan migrate:fresh
sail artisan db:seed

# Test development seeding
sail artisan db:seed-dev

# Test demo seeding
sail artisan db:seed-demo
```

### 13. Update Deployment Script

Edit `scripts/deploy.sh` and add seeding option:

```bash
#!/bin/bash

# Pacific Edge Labs - Deployment Script
# Usage: ./scripts/deploy.sh [--seed]

set -e

SEED_FLAG=""
if [[ "$1" == "--seed" ]]; then
    SEED_FLAG="--seed"
    echo "🌱 Seeding will be performed after deployment"
fi

echo "🚀 Starting deployment to Lightsail..."

# ... existing deployment code ...

ssh $REMOTE_HOST << ENDSSH
    # ... existing deployment code ...
    
    if [[ "$SEED_FLAG" == "--seed" ]]; then
        echo "🌱 Seeding demo data..."
        php artisan db:seed --class=ProductionDemoSeeder --force
    fi
    
    echo "✅ Deployment complete!"
ENDSSH

echo "🎉 Deployment successful!"
```

### 14. Document Seeder Usage

Create `docs/seeding.md`:

```markdown
# Database Seeding - Pacific Edge Labs

## Overview
Database seeders populate the application with test data for development and demo purposes.

## Seeder Types

### Basic Seeders (All Environments)
- **RoleSeeder** - Roles and permissions
- **UserSeeder** - Admin and test customer accounts

### Placeholder Seeders (For Future Phases)
- **ProductSeeder** - Product catalog (Phase 2)
- **BatchSeeder** - Product batches (Phase 2)
- **CoaSeeder** - Certificates of Analysis (Phase 3)
- **OrderSeeder** - Customer orders (Phase 4)

### Special Seeders
- **DevelopmentSeeder** - Extra verbose data for local testing
- **ProductionDemoSeeder** - Clean demo data for Lightsail

## Commands

### Local Development

```bash
# Fresh database with basic seeders
sail artisan migrate:fresh --seed

# Fresh database with development data (50+ users, lots of orders)
sail artisan db:seed-dev

# Just run seeders (no migration)
sail artisan db:seed
```

### Demo/Production

```bash
# Fresh database with demo data
php artisan db:seed-demo

# Add demo data to existing database
php artisan db:seed --class=ProductionDemoSeeder
```

### Individual Seeders

```bash
sail artisan db:seed --class=RoleSeeder
sail artisan db:seed --class=UserSeeder
```

## Test Accounts

### Super Admin
- Email: admin@pacificedgelabs.test
- Password: password

### Admin
- Email: staff@pacificedgelabs.test
- Password: password

### Demo Admin (Production Demo)
- Email: demo@pacificedgelabs.test
- Password: Set via DEMO_ADMIN_PASSWORD env variable

### Test Customers
- johnson1@research.test through brown10@research.test
- Password: password

## Deployment with Seeding

```bash
# Deploy and reseed demo data
./scripts/deploy.sh --seed
```

## Seeder Framework

### Using SeederHelpers Trait

```php
use App\Traits\SeederHelpers;

class MySeeder extends Seeder
{
    use SeederHelpers;
    
    public function run(): void
    {
        // Display progress
        $this->info('Seeding my data...');
        
        // Truncate table
        $this->truncateTable('my_table');
        
        // Check environment
        if ($this->shouldSeed('local')) {
            // Only run in local
        }
        
        // Random date
        $date = $this->randomDateBetween('2024-01-01', '2024-12-31');
    }
}
```

## Adding New Seeders (Future Phases)

1. Create seeder:
   ```bash
   sail artisan make:seeder MySeeder
   ```

2. Implement seeder logic

3. Add to `DatabaseSeeder.php`:
   ```php
   $this->call([
       MySeeder::class,
   ]);
   ```

4. Test:
   ```bash
   sail artisan db:seed --class=MySeeder
   ```

## Best Practices

### Idempotent Seeders
Check if data exists before creating:
```php
if (!User::where('email', 'admin@test.com')->exists()) {
    User::create([...]);
}
```

### Faker for Realistic Data
```php
$faker = \Faker\Factory::create();
$name = $faker->name;
$email = $faker->unique()->safeEmail;
```

### Seeder Dependencies
Use `$this->call()` to run seeders in order:
```php
$this->call([
    UserSeeder::class,
    ProductSeeder::class, // Needs users
    OrderSeeder::class, // Needs users and products
]);
```

### Progress Indicators
```php
$this->command->info('Creating products...');
$this->command->getOutput()->progressStart(100);

for ($i = 0; $i < 100; $i++) {
    // Create data
    $this->command->getOutput()->progressAdvance();
}

$this->command->getOutput()->progressFinish();
```

## Phase Roadmap

### Phase 0 ✅
- Role seeding
- User seeding
- Seeder framework

### Phase 2
- Expand ProductSeeder with real products
- Expand BatchSeeder with batches

### Phase 3
- Expand CoaSeeder with PDF generation

### Phase 4
- Expand OrderSeeder with realistic orders
- Add PaymentTransactionSeeder

### Phase 6
- Add admin audit log seeding

### Phase 7
- Add compliance log seeding
```

### 15. Commit Changes

```bash
git add .
git commit -m "Create database seeders framework with placeholder seeders"
git push
```

## Validation Checklist

- [ ] `sail artisan db:seed` runs successfully
- [ ] RoleSeeder creates all roles and permissions
- [ ] UserSeeder creates admin and customer accounts
- [ ] Can login with admin@pacificedgelabs.test
- [ ] Placeholder seeders exist for future phases
- [ ] `sail artisan db:seed-dev` works (creates 50+ customers)
- [ ] `sail artisan db:seed-demo` works
- [ ] SeederHelpers trait created
- [ ] Documentation created (seeding.md)
- [ ] Artisan commands registered

## Common Issues & Solutions

### Issue: "Class RoleSeeder not found"
**Solution:**
```bash
sail composer dump-autoload
```

### Issue: Duplicate entry errors
**Solution:**
Make seeders idempotent by checking existence:
```php
if (!Model::where('field', 'value')->exists()) {
    Model::create([...]);
}
```

### Issue: "Foreign key constraint fails"
**Solution:**
Ensure seeders run in correct order in `DatabaseSeeder`:
```php
$this->call([
    RoleSeeder::class, // First - no dependencies
    UserSeeder::class, // Second - needs roles
    ProductSeeder::class, // Later - may need users
]);
```

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-009 as complete
- ➡️ Proceed to TASK-0-010 (Testing Setup)

## Time Estimate
**45-60 minutes**

## Success Criteria
- Comprehensive seeder framework created
- All basic seeders working
- Placeholder seeders created for future phases
- Custom Artisan commands for easy seeding
- Documentation complete
- Can seed local, development, and demo databases
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001 through TASK-0-008  
**Blocks:** Demo data for Shane/Eldon testing  
**Priority:** High
