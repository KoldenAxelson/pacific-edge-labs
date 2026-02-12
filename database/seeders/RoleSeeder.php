<?php

namespace Database\Seeders;

use App\Traits\SeederHelpers;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Seeds roles and permissions for the application.
 *
 * Creates three default roles (super-admin, admin, customer) and assigns appropriate
 * permissions for user management, product management, order management, and settings.
 * Uses Spatie Laravel-permission package for role-based access control (RBAC).
 */
class RoleSeeder extends Seeder
{
    use SeederHelpers;

    /**
     * Seed all application roles and their associated permissions.
     *
     * Creates roles and permission definitions, then assigns permissions to roles
     * according to the application's authorization requirements.
     *
     * @return void
     */
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
