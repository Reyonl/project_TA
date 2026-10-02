<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use Database\Seeders\ProdukSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Dashboard access of the CURRENT architecture: two separate guarded
 * dashboards (customer.* behind auth:customer, admin.* behind auth:admin)
 * with route segregation between them.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): Customer
    {
        return Customer::create([
            'nama_customer' => 'Dash Customer',
            'email' => 'dash@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Test Address',
        ]);
    }

    private function admin(string $role = 'admin'): Admin
    {
        return Admin::create([
            'nama_admin' => 'Dash Admin',
            'email' => 'dash-admin@test.com',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function test_guest_is_redirected_to_login_from_customer_dashboard(): void
    {
        $response = $this->get(route('customer.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_customer_can_visit_customer_dashboard(): void
    {
        $this->seed(ProdukSeeder::class);
        $this->actingAs($this->customer(), 'customer');

        $response = $this->get(route('customer.dashboard'));

        $response->assertOk();
    }

    public function test_authenticated_admin_can_visit_admin_dashboard(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->customer(), 'customer');

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_customer_dashboard(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $response = $this->get(route('customer.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
