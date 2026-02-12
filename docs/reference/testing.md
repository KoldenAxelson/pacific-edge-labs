# Testing Guide - Pacific Edge Labs

## Overview

Comprehensive testing ensures code quality and prevents regressions. We use PHPUnit for all automated testing. Tests run against a dedicated `pacific_edge_labs_test` database and are isolated via `RefreshDatabase`.

## Test Types

### Feature Tests
Test complete user flows and HTTP endpoints.
Location: `tests/Feature/`

### Unit Tests
Test individual classes and methods in isolation.
Location: `tests/Unit/`

## Running Tests

```bash
# All tests
sail artisan test

# Specific test file
sail artisan test tests/Feature/Auth/LoginTest.php

# Specific test method
sail artisan test --filter=test_users_can_authenticate

# With coverage (requires Xdebug)
sail artisan test --coverage

# Parallel (faster)
sail artisan test --parallel
```

## Test Database

Tests use a separate `pacific_edge_labs_test` database configured in `.env.testing`. The database is migrated fresh automatically via `RefreshDatabase` — no manual setup needed between runs.

To create the test database for the first time:

```bash
sail artisan --env=testing migrate:fresh
```

## Test Helpers

### Acting As a Role

```php
// Create and authenticate as super-admin
$this->actingAsSuperAdmin();

// Create and authenticate as admin
$this->actingAsAdmin();

// Create and authenticate as customer
$this->actingAsCustomer();
```

### Creating Users

```php
// Basic user
$user = $this->createUser();

// User with specific attributes
$user = $this->createUser(['email' => 'custom@test.com']);

// User with role
$admin = $this->createUserWithRole('admin');

// Multiple users with role (via TestHelpers trait)
$customers = $this->createUsersWithRole('customer', 5);
```

### Role/Permission Assertions (via TestHelpers trait)

```php
$this->assertUserHasRole($user, 'admin');
$this->assertUserCan($user, 'manage products');
$this->assertUserCannot($user, 'manage users');
```

### Database Assertions

```php
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);
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

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php      # Login/logout (Breeze default)
│   │   ├── RegistrationTest.php        # Registration + customer role assignment
│   │   ├── PasswordResetTest.php       # Password reset flow
│   │   ├── PasswordUpdateTest.php      # Password change
│   │   ├── EmailVerificationTest.php   # Email verification
│   │   └── PasswordConfirmationTest.php
│   ├── AdminPanelAccessTest.php        # Filament access by role
│   └── RolePermissionTest.php          # Spatie role/permission coverage
├── Unit/
│   └── Services/
│       └── PaymentServiceTest.php      # MockPaymentGateway behavior
└── Helpers/
    └── TestHelpers.php                 # Reusable assertion/creation helpers
```

### Naming Conventions
- Test files: `{Feature}Test.php`
- Test methods: `test_{what_it_tests}(): void`

## Best Practices

**Use RefreshDatabase** — rolls back the database after each test, keeping tests isolated.

**Arrange-Act-Assert** — every test should follow this structure clearly:
```php
public function test_something(): void
{
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)->post('/endpoint');

    // Assert
    $response->assertStatus(200);
}
```

**One behavior per test** — don't bundle multiple assertions that test different behaviors into a single test method.

**Use factories for test data** — factories are flexible and avoid brittle hardcoded values.

**Seed in base TestCase** — `protected bool $seed = true` ensures roles and permissions exist for every test that needs them.

## Coverage Goals

| Area              | Target |
|-------------------|--------|
| Authentication    | 100%   |
| Roles/Permissions | 100%   |
| Payment Service   | 90%+   |
| Overall (Phase 0) | 80%+   |

Critical paths (checkout, payments) target 100% when those phases are implemented.

## Common Issues

**"Database does not exist"** — Run `sail artisan --env=testing migrate:fresh` once to create the test database.

**"Class not found" errors** — Run `sail composer dump-autoload`.

**Tests affect each other** — Ensure `RefreshDatabase` is applied. Avoid static state or singletons that persist between tests.

**Permissions not found in tests** — Check that `protected bool $seed = true` is set in `TestCase.php`. The `RoleSeeder` must run before any test that calls `hasRole()` or `can()`.

## CI/CD (Phase 7)

Tests will run automatically on every push and pull request via GitHub Actions.
