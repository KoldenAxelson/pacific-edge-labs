<?php

namespace Tests\Helpers;

use App\Models\User;

/**
 * Provides reusable assertion and creation helpers for test cases.
 *
 * Includes utilities for creating users with roles, asserting user role assignments,
 * and verifying user permissions and capabilities.
 */
trait TestHelpers
{
    /**
     * Create multiple users with a given role.
     */
    protected function createUsersWithRole(string $role, int $count): \Illuminate\Support\Collection
    {
        return User::factory()
            ->count($count)
            ->create()
            ->each(fn ($user) => $user->assignRole($role));
    }

    /**
     * Assert user has specific role.
     */
    protected function assertUserHasRole(User $user, string $role): void
    {
        $this->assertTrue(
            $user->hasRole($role),
            "User does not have expected role: {$role}"
        );
    }

    /**
     * Assert user can perform action.
     */
    protected function assertUserCan(User $user, string $permission): void
    {
        $this->assertTrue(
            $user->can($permission),
            "User cannot perform action: {$permission}"
        );
    }

    /**
     * Assert user cannot perform action.
     */
    protected function assertUserCannot(User $user, string $permission): void
    {
        $this->assertFalse(
            $user->can($permission),
            "User can unexpectedly perform action: {$permission}"
        );
    }
}
