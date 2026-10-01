<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Desain;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Order state machine of the CURRENT application:
 *
 *   checkout            -> status_order=reviewing,       payment_status=unpaid
 *   approve all designs -> status_order=pending_payment,  payment_status=awaiting_payment
 *   upload proof        -> payment_status=awaiting_verification (only when awaiting_payment|failed)
 *   admin approve       -> status_order=processing,       payment_status=paid
 *   admin reject        -> status_order=pending_payment,  payment_status=failed
 */
class OrderTransitionTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private Admin $admin;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama_customer' => 'Flow Customer',
            'email' => 'flow@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Test Address',
        ]);

        $this->admin = Admin::create([
            'nama_admin' => 'Flow Admin',
            'email' => 'flow-admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->produk = Produk::create([
            'nama_produk' => 'Kaos Test',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 50000,
            'deskripsi' => 'test product',
        ]);
    }

    private function makeOrder(string $statusOrder = 'reviewing', string $paymentStatus = 'unpaid'): Order
    {
        $desain = Desain::create([
            'id_customer' => $this->customer->id_customer,
            'file_desain' => 'desain/x.png',
            'harga_desain' => 25000,
            'detail_sablon' => '1x A3',
            'warna_baju' => '#ffffff',
        ]);

        $order = Order::create([
            'id_customer' => $this->customer->id_customer,
            'tanggal_order' => now(),
            'status_order' => $statusOrder,
            'payment_status' => $paymentStatus,
            'total_harga' => 75000,
        ]);

        OrderDetail::create([
            'id_order' => $order->id_order,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => $desain->id_desain,
            'quantity' => 1,
            'harga_produk' => 50000,
            'harga_desain' => 25000,
            'subtotal' => 75000,
            'status_desain' => 'pending',
        ]);

        return $order->fresh()->load('orderDetails');
    }

    public function test_approving_all_designs_moves_reviewing_order_to_pending_payment(): void
    {
        $order = $this->makeOrder();
        $detail = $order->orderDetails->first();

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.updateStatusDesain', [$order->id_order, $detail->id_order_detail]), [
                'status_desain' => 'approved',
                'catatan_admin' => 'OK',
            ]);

        $order->refresh();
        $this->assertEquals('pending_payment', $order->status_order);
        $this->assertEquals('awaiting_payment', $order->payment_status);
    }

    public function test_customer_can_upload_proof_when_awaiting_payment(): void
    {
        Storage::fake('public');
        $order = $this->makeOrder('pending_payment', 'awaiting_payment');

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.orders.payment', $order->id_order), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
            ])->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('awaiting_verification', $order->payment_status);
        $this->assertNotNull($order->bukti_pembayaran);
    }

    public function test_customer_cannot_upload_proof_while_unpaid_reviewing(): void
    {
        Storage::fake('public');
        $order = $this->makeOrder('reviewing', 'unpaid');

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.orders.payment', $order->id_order), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
            ])->assertSessionHas('error');

        $this->assertEquals('unpaid', $order->fresh()->payment_status);
    }

    public function test_customer_can_upload_proof_again_after_rejection(): void
    {
        Storage::fake('public');
        $order = $this->makeOrder('pending_payment', 'failed');

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.orders.payment', $order->id_order), [
                'bukti_pembayaran' => UploadedFile::fake()->image('retry.jpg'),
            ])->assertSessionHas('success');

        $this->assertEquals('awaiting_verification', $order->fresh()->payment_status);
    }

    public function test_admin_payment_approval_moves_to_processing_and_paid(): void
    {
        $order = $this->makeOrder('pending_payment', 'awaiting_verification');

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.verifyPayment', $order->id_order), [
                'action' => 'approve',
            ]);

        $order->refresh();
        $this->assertEquals('processing', $order->status_order);
        $this->assertEquals('paid', $order->payment_status);
    }

    public function test_admin_payment_rejection_kicks_back_to_pending_payment(): void
    {
        $order = $this->makeOrder('pending_payment', 'awaiting_verification');

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.verifyPayment', $order->id_order), [
                'action' => 'reject',
            ]);

        $order->refresh();
        $this->assertEquals('pending_payment', $order->status_order);
        $this->assertEquals('failed', $order->payment_status);
    }

    public function test_manual_status_update_validates_allowed_states(): void
    {
        $order = $this->makeOrder();

        // Invalid enum value must be rejected by the validator.
        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.updateStatus', $order->id_order), [
                'status_order' => 'shipped',
            ])->assertSessionHasErrors('status_order');

        // Valid transition is accepted.
        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.updateStatus', $order->id_order), [
                'status_order' => 'completed',
            ]);

        $this->assertEquals('completed', $order->fresh()->status_order);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $order = $this->makeOrder();

        $other = Customer::create([
            'nama_customer' => 'Other',
            'email' => 'other@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '081299999999',
            'alamat' => 'Elsewhere',
        ]);

        // Order lookup is scoped by id_customer + findOrFail -> foreign order id 404s.
        $this->actingAs($other, 'customer')
            ->get(route('customer.orders.show', $order->id_order))
            ->assertNotFound();
    }

    public function test_checkout_rejects_empty_or_invalid_cart_selection(): void
    {
        $this->actingAs($this->customer, 'customer');

        // StoreCheckoutRequest: cart_ids is required|array|min:1 + exists:carts.
        $this->from(route('customer.cart.index'))
            ->post(route('customer.checkout.store'), [])
            ->assertRedirect(route('customer.cart.index'))
            ->assertSessionHasErrors('cart_ids');

        $this->assertDatabaseMissing('orders', ['id_customer' => $this->customer->id_customer]);
    }

    public function test_checkout_ignores_cart_rows_that_are_not_yours(): void
    {
        // Cart ids that exist but belong to another customer: the controller filters
        // by id_customer, finds nothing, and creates no order.
        $other = Customer::create([
            'nama_customer' => 'Foreign Cart Owner',
            'email' => 'cartowner@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '081288888888',
            'alamat' => 'Elsewhere',
        ]);
        $foreignCart = Cart::create([
            'id_customer' => $other->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.checkout.store'), ['cart_ids' => [$foreignCart->id_cart]]);

        $this->assertDatabaseMissing('orders', ['id_customer' => $this->customer->id_customer]);
    }

    public function test_checkout_uses_priced_cart_and_creates_reviewing_order(): void
    {
        $cart = Cart::create([
            'id_customer' => $this->customer->id_customer,
            'id_produk' => $this->produk->id_produk,
            'id_desain' => null,
            'quantity' => 2,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.checkout.store'), [
                'cart_ids' => [$cart->id_cart],
            ]);

        $order = Order::where('id_customer', $this->customer->id_customer)->first();
        $this->assertNotNull($order);
        $this->assertEquals('reviewing', $order->status_order);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals(100000, $order->total_harga); // (50000 + 0) * 2
        $this->assertDatabaseMissing('carts', ['id_cart' => $cart->id_cart]);
    }
}
