<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case with role and permission helper methods.
 *
 * Provides convenient factory methods for creating test users with specific roles,
 * acting as authenticated users, and accessing the full permissions system.
 * Automatically seeds the database with roles and permissions before each test.
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     * Ensures roles and permissions exist for every test that needs them.
     */
    protected bool $seed = true;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['telescope.enabled' => false]);
    }

    /**
     * Create a user.
     *
     * @param array $attributes Optional attributes to override defaults
     * @return \App\Models\User The created user
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create a user with a specific role.
     *
     * @param string $role The role to assign to the user
     * @param array $attributes Optional attributes to override defaults
     * @return \App\Models\User The created user with assigned role
     */
    protected function createUserWithRole(string $role, array $attributes = []): \App\Models\User
    {
        $user = $this->createUser($attributes);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Create and authenticate a super-admin.
     *
     * @param array $attributes Optional attributes to override defaults
     * @return static The test case instance authenticated as super-admin
     */
    protected function actingAsSuperAdmin(array $attributes = []): static
    {
        $admin = $this->createUserWithRole('super-admin', $attributes);

        return $this->actingAs($admin);
    }

    /**
     * Create and authenticate an admin.
     *
     * @param array $attributes Optional attributes to override defaults
     * @return static The test case instance authenticated as admin
     */
    protected function actingAsAdmin(array $attributes = []): static
    {
        $admin = $this->createUserWithRole('admin', $attributes);

        return $this->actingAs($admin);
    }

    /**
     * Create and authenticate a customer.
     *
     * @param array $attributes Optional attributes to override defaults
     * @return static The test case instance authenticated as customer
     */
    protected function actingAsCustomer(array $attributes = []): static
    {
        $customer = $this->createUserWithRole('customer', $attributes);

        return $this->actingAs($customer);
    }
}
