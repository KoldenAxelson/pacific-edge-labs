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
