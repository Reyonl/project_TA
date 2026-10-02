<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Authentication of the CURRENT architecture: one /login endpoint that
 * authenticates against the customer guard first, then the admin guard
 * (App\Http\Requests\Auth\LoginRequest), with role-based redirect target.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomer(string $email = 'customer@test.com'): Customer
    {
        return Customer::create([
            'nama_customer' => 'Test Customer',
            'email' => $email,
            'password' => Hash::make('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Test Address',
        ]);
    }

    private function makeAdmin(string $email = 'admin@test.com', string $role = 'admin'): Admin
    {
        return Admin::create([
            'nama_admin' => 'Test Admin',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_customer_can_authenticate_and_redirects_to_customer_dashboard(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        // Login is recorded on the customer guard; the default (web) guard stays empty.
        $this->assertAuthenticatedAs($customer, 'customer');
        $response->assertRedirect(route('customer.dashboard', absolute: false));
    }

    public function test_admin_can_authenticate_and_redirects_to_admin_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertEquals($admin->id_admin, Auth::guard('admin')->id());
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $this->makeCustomer();

        $response = $this->post('/login', [
            'email' => 'customer@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_logout_clears_the_authenticated_guard(): void
    {
        $customer = $this->makeCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->post('/logout');

        $this->assertGuest('customer');
        $response->assertRedirect('/');
    }
}
