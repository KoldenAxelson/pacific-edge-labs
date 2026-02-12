<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
