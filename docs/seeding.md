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
- **DevelopmentSeeder** - Extra verbose data for local testing (50+ users)
- **ProductionDemoSeeder** - Clean demo data for Lightsail

## Commands

### Local Development

```bash
# Fresh database with basic seeders
sail artisan migrate:fresh --seed

# Fresh database with development data (50+ users)
sail artisan db:seed-dev

# Just run seeders (no migration)
sail artisan db:seed
```

### Demo/Production

```bash
# Fresh database with demo data (prompts for confirmation)
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
- Password: Set via `DEMO_ADMIN_PASSWORD` env variable

### Test Customers
- johnson1@research.test through brown10@research.test
- Password: password

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

        // Truncate table (PostgreSQL: resets identity, cascades)
        $this->truncateTable('my_table');

        // Check environment
        if ($this->shouldSeed('local')) {
            // Only run in local
        }

        // Random date between two dates
        $date = $this->randomDateBetween('2024-01-01', '2024-12-31');
    }
}
```

## Adding New Seeders (Future Phases)

1. Create the seeder file in `database/seeders/`

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
Check if data exists before creating to allow safe re-runs:
```php
if (!User::where('email', 'admin@test.com')->exists()) {
    User::create([...]);
}
```

### Seeder Dependencies
Use `$this->call()` to run seeders in the correct order:
```php
$this->call([
    RoleSeeder::class,    // First - no dependencies
    UserSeeder::class,    // Second - needs roles
    ProductSeeder::class, // Later - may need categories
    BatchSeeder::class,   // After products
    OrderSeeder::class,   // Needs users and products
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
- Role seeding (roles and permissions)
- User seeding (admins and test customers)
- Seeder framework (SeederHelpers trait, Artisan commands)

### Phase 2
- Expand ProductSeeder with real products (BPC-157, TB-500, etc.)
- Expand BatchSeeder with batches linked to products

### Phase 3
- Expand CoaSeeder with PDF generation and S3 upload

### Phase 4
- Expand OrderSeeder with realistic multi-product orders
- Add PaymentTransactionSeeder

### Phase 6
- Add admin audit log seeding

### Phase 7
- Add compliance log seeding
- Full demo dataset for Shane/Eldon presentation
