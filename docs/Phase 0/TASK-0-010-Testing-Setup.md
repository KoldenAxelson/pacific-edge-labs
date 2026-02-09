# [TASK-0-010] Testing Setup & First Feature Test

## Overview
Set up PHPUnit testing framework, configure test database, create base test classes, and write first feature tests to verify authentication and core functionality.

## Prerequisites
- [x] All previous Phase 0 tasks completed
- [x] Laravel application working locally

## Goals
- Configure PHPUnit for Laravel testing
- Set up separate test database
- Create base test case classes
- Write authentication tests
- Write role/permission tests
- Create testing helpers
- Document testing practices

## Step-by-Step Instructions

### 1. Verify PHPUnit Installation

PHPUnit comes with Laravel by default. Verify:

```bash
sail artisan test
```

You should see Laravel's default tests pass.

### 2. Configure Test Environment

Create `.env.testing`:

```env
APP_NAME="Pacific Edge Labs"
APP_ENV=testing
APP_KEY=base64:testing_key_will_be_generated
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=pacific_edge_labs_test
DB_USERNAME=sail
DB_PASSWORD=password

# Use array cache for testing (faster)
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync

# Mail should not send in tests
MAIL_MAILER=array

# Disable Telescope in tests
TELESCOPE_ENABLED=false

# Mock payment gateway
PAYMENT_GATEWAY=mock
PAYMENT_TEST_MODE=true

# AWS - use fake disk in tests
AWS_ACCESS_KEY_ID=testing
AWS_SECRET_ACCESS_KEY=testing
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=testing
```

### 3. Generate Testing App Key

```bash
php artisan key:generate --env=testing
```

### 4. Create Test Database

```bash
sail artisan db:create pacific_edge_labs_test || true
sail artisan --env=testing migrate:fresh
```

### 5. Configure PHPUnit

Edit `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="pgsql"/>
        <env name="DB_DATABASE" value="pacific_edge_labs_test"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

### 6. Create Base Test Case

Edit `tests/TestCase.php`:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     */
    protected bool $seed = true;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable Telescope during tests
        config(['telescope.enabled' => false]);
    }

    /**
     * Create an authenticated user
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create an authenticated user with specific role
     */
    protected function createUserWithRole(string $role, array $attributes = []): \App\Models\User
    {
        $user = $this->createUser($attributes);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Create and authenticate a super admin
     */
    protected function actingAsSuperAdmin(array $attributes = []): static
    {
        $admin = $this->createUserWithRole('super-admin', $attributes);
        return $this->actingAs($admin);
    }

    /**
     * Create and authenticate an admin
     */
    protected function actingAsAdmin(array $attributes = []): static
    {
        $admin = $this->createUserWithRole('admin', $attributes);
        return $this->actingAs($admin);
    }

    /**
     * Create and authenticate a customer
     */
    protected function actingAsCustomer(array $attributes = []): static
    {
        $customer = $this->createUserWithRole('customer', $attributes);
        return $this->actingAs($customer);
    }
}
```

### 7. Create Authentication Tests

```bash
sail artisan make:test Auth/RegistrationTest
```

Edit `tests/Feature/Auth/RegistrationTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_registered_users_are_assigned_customer_role(): void
    {
        $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'customer@example.com')->first();
        
        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
```

### 8. Create Login Tests

```bash
sail artisan make:test Auth/LoginTest
```

Edit `tests/Feature/Auth/LoginTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
```

### 9. Create Role & Permission Tests

```bash
sail artisan make:test RolePermissionTest
```

Edit `tests/Feature/RolePermissionTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_seeded_correctly(): void
    {
        $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'customer']);
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = Role::findByName('super-admin');
        $allPermissions = Permission::all();

        foreach ($allPermissions as $permission) {
            $this->assertTrue($superAdmin->hasPermissionTo($permission));
        }
    }

    public function test_admin_has_selected_permissions(): void
    {
        $admin = Role::findByName('admin');

        $this->assertTrue($admin->hasPermissionTo('view admin panel'));
        $this->assertTrue($admin->hasPermissionTo('manage products'));
        $this->assertFalse($admin->hasPermissionTo('manage users'));
    }

    public function test_customer_has_no_admin_permissions(): void
    {
        $customer = Role::findByName('customer');

        $this->assertFalse($customer->hasPermissionTo('view admin panel'));
        $this->assertFalse($customer->hasPermissionTo('manage products'));
    }

    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['admin', 'customer']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_user_permissions_include_role_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->can('view admin panel'));
        $this->assertTrue($user->can('manage products'));
    }
}
```

### 10. Create Admin Panel Access Tests

```bash
sail artisan make:test AdminPanelAccessTest
```

Edit `tests/Feature/AdminPanelAccessTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $customer = $this->createUserWithRole('customer');

        $response = $this->actingAs($customer)->get('/admin');

        // Filament handles this, might redirect or show 403
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_admin_panel(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');

        $response = $this->actingAs($superAdmin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_users_resource(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }
}
```

### 11. Create Payment Service Tests

```bash
sail artisan make:test Unit/PaymentServiceTest --unit
```

Edit `tests/Unit/PaymentServiceTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Contracts\PaymentGatewayInterface;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentService = app(PaymentService::class);
        $this->user = User::factory()->create();
    }

    public function test_successful_payment_creates_transaction(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertInstanceOf(PaymentTransaction::class, $transaction);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(99.99, $transaction->amount);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    public function test_failed_payment_creates_failed_transaction(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 49.99,
            paymentDetails: [
                'card_number' => '4111111111110000', // Ends in 0000 = fails
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertEquals('failed', $transaction->status);
        $this->assertNotNull($transaction->error_message);
    }

    public function test_payment_method_is_masked(): void
    {
        $transaction = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        $this->assertStringContainsString('****1111', $transaction->payment_method);
        $this->assertStringContainsString('Visa', $transaction->payment_method);
    }

    public function test_refund_creates_refund_transaction(): void
    {
        // Create successful payment
        $payment = $this->paymentService->processPayment(
            user: $this->user,
            amount: 99.99,
            paymentDetails: [
                'card_number' => '4111111111111111',
                'cvv' => '123',
                'expiry' => '12/25',
                'name' => $this->user->name,
            ]
        );

        // Process refund
        $result = $this->paymentService->processRefund($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('refunded', $payment->fresh()->status);
        $this->assertDatabaseHas('payment_transactions', [
            'type' => 'refund',
            'user_id' => $this->user->id,
        ]);
    }
}
```

### 12. Create Test Helpers

Create `tests/Helpers/TestHelpers.php`:

```php
<?php

namespace Tests\Helpers;

use App\Models\User;

trait TestHelpers
{
    /**
     * Create multiple users with role
     */
    protected function createUsersWithRole(string $role, int $count): \Illuminate\Support\Collection
    {
        return User::factory()
            ->count($count)
            ->create()
            ->each(fn($user) => $user->assignRole($role));
    }

    /**
     * Assert user has specific role
     */
    protected function assertUserHasRole(User $user, string $role): void
    {
        $this->assertTrue(
            $user->hasRole($role),
            "User does not have expected role: {$role}"
        );
    }

    /**
     * Assert user can perform action
     */
    protected function assertUserCan(User $user, string $permission): void
    {
        $this->assertTrue(
            $user->can($permission),
            "User cannot perform action: {$permission}"
        );
    }

    /**
     * Assert user cannot perform action
     */
    protected function assertUserCannot(User $user, string $permission): void
    {
        $this->assertFalse(
            $user->can($permission),
            "User can unexpectedly perform action: {$permission}"
        );
    }
}
```

### 13. Run All Tests

```bash
sail artisan test
```

You should see all tests passing.

### 14. Run Tests with Coverage (Optional)

```bash
sail artisan test --coverage
```

This shows which parts of your code are covered by tests.

### 15. Create Testing Documentation

Create `docs/testing.md`:

```markdown
# Testing Guide - Pacific Edge Labs

## Overview
Comprehensive testing ensures code quality and prevents regressions. We use PHPUnit for all automated testing.

## Test Types

### Feature Tests
Test complete user flows and HTTP endpoints.  
Location: `tests/Feature/`

### Unit Tests
Test individual classes and methods in isolation.  
Location: `tests/Unit/`

## Running Tests

### All Tests
```bash
sail artisan test
```

### Specific Test File
```bash
sail artisan test tests/Feature/Auth/LoginTest.php
```

### Specific Test Method
```bash
sail artisan test --filter=test_users_can_authenticate
```

### With Coverage
```bash
sail artisan test --coverage
```

### Parallel Testing (Faster)
```bash
sail artisan test --parallel
```

## Test Database

Tests use a separate `pacific_edge_labs_test` database configured in `.env.testing`.

### Reset Test Database
```bash
sail artisan --env=testing migrate:fresh
```

## Writing Tests

### Basic Feature Test Structure
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_something_works(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/some-route');
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('some_table', ['field' => 'value']);
    }
}
```

### Basic Unit Test Structure
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;

class MyUnitTest extends TestCase
{
    public function test_method_returns_expected_value(): void
    {
        $service = new MyService();
        $result = $service->doSomething();
        
        $this->assertEquals('expected', $result);
    }
}
```

## Test Helpers

### Acting As User with Role
```php
// Act as super admin
$this->actingAsSuperAdmin();

// Act as regular admin
$this->actingAsAdmin();

// Act as customer
$this->actingAsCustomer();
```

### Creating Users
```php
// Create basic user
$user = $this->createUser();

// Create user with specific attributes
$user = $this->createUser(['email' => 'custom@test.com']);

// Create user with role
$admin = $this->createUserWithRole('admin');
```

### Database Assertions
```php
// Assert record exists
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

// Assert record doesn't exist
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);

// Assert count
$this->assertDatabaseCount('users', 5);
```

### HTTP Assertions
```php
$response->assertStatus(200);
$response->assertRedirect('/dashboard');
$response->assertJson(['success' => true]);
$response->assertSessionHas('message');
$response->assertSessionHasErrors('email');
```

## Test Organization

### Naming Convention
- Test files: `{Feature}Test.php`
- Test methods: `test_{what_it_tests}(): void`

### Directory Structure
```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegistrationTest.php
│   ├── Admin/
│   │   └── UserManagementTest.php
│   └── ...
├── Unit/
│   ├── Services/
│   │   └── PaymentServiceTest.php
│   └── ...
└── Helpers/
    └── TestHelpers.php
```

## Best Practices

### 1. Use Database Transactions
```php
use RefreshDatabase; // Rolls back after each test
```

### 2. One Assertion Per Test
Focus each test on one specific behavior.

### 3. Descriptive Test Names
```php
// Good
public function test_admin_can_create_product(): void

// Bad
public function test_create(): void
```

### 4. Arrange-Act-Assert Pattern
```php
public function test_something(): void
{
    // Arrange - Set up test data
    $user = User::factory()->create();
    
    // Act - Perform the action
    $response = $this->actingAs($user)->post('/endpoint');
    
    // Assert - Verify the result
    $response->assertStatus(200);
}
```

### 5. Use Factories for Test Data
```php
// Good - Flexible, reusable
$user = User::factory()->create(['email' => 'test@example.com']);

// Bad - Hardcoded, brittle
$user = new User(['name' => 'Test', 'email' => 'test@example.com', ...]);
```

## CI/CD Integration (Phase 7)

Tests will run automatically on:
- Every push to GitHub
- Every pull request
- Before deployment

### GitHub Actions Workflow (Future)
```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run tests
        run: php artisan test
```

## Coverage Goals

### Phase 0
- Authentication: 100%
- Role/Permissions: 100%
- Payment Service: 90%

### Future Phases
- Aim for 80%+ overall coverage
- 100% coverage for critical paths (checkout, payments)

## Common Pitfalls

### Issue: Tests affect each other
**Solution:** Use `RefreshDatabase` trait

### Issue: Factory not found
**Solution:** Create factory:
```bash
sail artisan make:factory ProductFactory --model=Product
```

### Issue: Database locked
**Solution:** Use separate test database, never run tests against production

## Next Phase Testing

### Phase 2: Products
- Product CRUD tests
- Batch management tests
- Inventory tracking tests

### Phase 3: CoAs
- CoA upload tests
- PDF generation tests
- S3 storage tests

### Phase 4: Checkout
- Cart management tests
- Order creation tests
- Payment processing tests

## Resources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Pest PHP](https://pestphp.com/) - Alternative testing framework (optional)
```

### 16. Commit Changes

```bash
git add .
git commit -m "Set up comprehensive testing framework with feature and unit tests"
git push
```

## Validation Checklist

- [ ] `sail artisan test` runs successfully
- [ ] All authentication tests pass
- [ ] Role/permission tests pass
- [ ] Admin panel access tests pass
- [ ] Payment service tests pass
- [ ] `.env.testing` configured
- [ ] Test database created
- [ ] Base TestCase with helpers created
- [ ] At least 15+ tests passing
- [ ] Documentation created (testing.md)

## Common Issues & Solutions

### Issue: "Database does not exist"
**Solution:**
Create test database:
```bash
sail artisan --env=testing migrate:fresh
```

### Issue: Tests fail with "Cannot modify header information"
**Solution:**
Ensure no output before test assertions. Check for `dd()`, `dump()`, or `echo` statements.

### Issue: "Class not found" in tests
**Solution:**
```bash
sail composer dump-autoload
sail artisan test
```

### Issue: Permissions errors in tests
**Solution:**
Ensure roles and permissions are seeded:
```php
protected bool $seed = true; // In TestCase
```

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-010 as complete
- ✅ **PHASE 0 COMPLETE!**
- ➡️ Proceed to TASK-1-000 (Design System & Brand Foundation)

## Time Estimate
**60-90 minutes**

## Success Criteria
- PHPUnit configured and working
- Test database set up
- Base test case with helpers created
- 15+ tests written and passing
- Authentication fully tested
- Roles/permissions fully tested
- Payment service tested
- Admin panel access tested
- Documentation complete
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-001 through TASK-0-009  
**Blocks:** None - Phase 0 complete!  
**Priority:** High (ensures code quality going forward)
