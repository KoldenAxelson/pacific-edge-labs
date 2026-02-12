<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for admin panel access control.
 *
 * Verifies that authentication and authorization rules are properly enforced
 * for admin panel routes based on user roles and permissions.
 */
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
