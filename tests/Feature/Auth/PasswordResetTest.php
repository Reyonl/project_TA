<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Password reset of the CURRENT custom flow (PasswordResetLinkController::store):
 * identity is verified by the last 3 digits of the registered phone number and
 * the password is updated immediately — no token/notification round-trip.
 * Admin accounts are intentionally refused self-service reset.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'nama_customer' => 'Reset Customer',
            'email' => 'reset@test.com',
            'password' => Hash::make('old-password'),
            'no_hp' => '08123456789', // last 3 digits: 789
            'alamat' => 'Test Address',
        ]);
    }

    public function test_reset_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_unknown_email_is_rejected(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nobody@test.com',
            'phone_last_3' => '789',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_email_cannot_self_reset(): void
    {
        Admin::create([
            'nama_admin' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'admin@test.com',
            'phone_last_3' => '789',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_wrong_phone_digits_do_not_reset_password(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->post('/forgot-password', [
            'email' => $customer->email,
            'phone_last_3' => '000',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('phone_last_3');
        $this->assertTrue(Hash::check('old-password', $customer->fresh()->password));
    }

    public function test_customer_can_reset_with_correct_phone_digits(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->post('/forgot-password', [
            'email' => $customer->email,
            'phone_last_3' => '789',
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('brand-new-password', $customer->fresh()->password));

        // And the new password actually authenticates on the customer guard.
        $login = $this->post('/login', [
            'email' => $customer->email,
            'password' => 'brand-new-password',
        ]);
        $login->assertRedirect(route('customer.dashboard', absolute: false));
    }
}
