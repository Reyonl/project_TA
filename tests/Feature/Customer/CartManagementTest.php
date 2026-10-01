<?php

namespace Tests\Feature\Customer;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Cart management of the CURRENT architecture (CartController):
 * direct add of ready-made products, quantity updates, removal —
 * all ownership-scoped to the authenticated customer.
 */
class CartManagementTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama_customer' => 'Cart Customer',
            'email' => 'cart@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Test Address',
        ]);

        $this->produk = Produk::create([
            'nama_produk' => 'Kaos Jadi',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'jadi',
            'harga_dasar' => 60000,
        ]);
    }

    public function test_customer_can_add_ready_made_product_to_cart(): void
    {
        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.cart.storeDirect', $this->produk->id_produk), [
                'quantity' => 3,
            ])
            ->assertRedirect(route('customer.cart.index'));

        $this->assertDatabaseHas('carts', [
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'quantity' => 3,
        ]);
    }

    public function test_direct_add_validates_quantity(): void
    {
        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.cart.storeDirect', $this->produk->id_produk), [
                'quantity' => 0,
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertEquals(0, Cart::count());
    }

    public function test_customer_can_update_cart_quantity(): void
    {
        $cart = Cart::create([
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->patch(route('customer.cart.updateQuantity', $cart->id_cart), [
                'quantity' => 5,
            ])
            ->assertJson(['success' => true]);

        $this->assertEquals(5, $cart->fresh()->quantity);
    }

    public function test_customer_cannot_update_another_customers_cart_row(): void
    {
        $other = Customer::create([
            'nama_customer' => 'Other',
            'email' => 'other@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '081299999999',
            'alamat' => 'Elsewhere',
        ]);

        $cart = Cart::create([
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 1,
        ]);

        $this->actingAs($other, 'customer')
            ->patch(route('customer.cart.updateQuantity', $cart->id_cart), [
                'quantity' => 99,
            ])
            ->assertForbidden();

        $this->assertEquals(1, $cart->fresh()->quantity);
    }

    public function test_customer_can_delete_own_cart_row(): void
    {
        $cart = Cart::create([
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->delete(route('customer.cart.destroy', $cart->id_cart));

        $this->assertDatabaseMissing('carts', ['id_cart' => $cart->id_cart]);
    }

    public function test_customer_cannot_delete_another_customers_cart_row(): void
    {
        $other = Customer::create([
            'nama_customer' => 'Other2',
            'email' => 'other2@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '081299999998',
            'alamat' => 'Elsewhere',
        ]);

        $cart = Cart::create([
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 1,
        ]);

        $this->actingAs($other, 'customer')
            ->delete(route('customer.cart.destroy', $cart->id_cart))
            ->assertForbidden();

        $this->assertDatabaseHas('carts', ['id_cart' => $cart->id_cart]);
    }
}
