<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Registration of the CURRENT architecture: /register creates a Customer
 * (RegisteredUserController) and logs them into the `customer` guard.
 * Requires name, email, no_hp, alamat — not the legacy name/email/password trio.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'no_hp' => '081299999999',
            'alamat' => 'Jalan Test No. 1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'new@example.com',
            'nama_customer' => 'New Customer',
        ]);

        $response->assertRedirect(route('customer.dashboard', absolute: false));

        // Authenticated on the customer guard (not the legacy default guard).
        $this->assertAuthenticatedAs(
            Customer::where('email', 'new@example.com')->first(),
            'customer'
        );
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        Customer::create([
            'nama_customer' => 'Existing',
            'email' => 'dup@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081200000000',
            'alamat' => 'Alamat',
        ]);

        $response = $this->post('/register', [
            'name' => 'Another',
            'email' => 'dup@example.com',
            'no_hp' => '081211111111',
            'alamat' => 'Alamat Lain',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, Customer::where('email', 'dup@example.com')->count());
    }

    public function test_registration_requires_current_fields(): void
    {
        // no_hp and alamat are required by the current validator — legacy-style
        // registration payloads (name/email/password only) must fail validation.
        $response = $this->post('/register', [
            'name' => 'Incomplete',
            'email' => 'incomplete@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['no_hp', 'alamat']);
        $this->assertDatabaseMissing('customers', ['email' => 'incomplete@example.com']);
    }
}
