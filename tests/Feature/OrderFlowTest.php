<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Desain;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ProdukSeeder;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_complete_entire_order_flow()
    {
        Storage::fake('public');

        // 1. Setup Data
        $this->seed([
            AdminSeeder::class,
            ProdukSeeder::class
        ]);

        $customer = Customer::create([
            'nama_customer' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Test Address'
        ]);

        $produk = Produk::where('jenis_produk', 'kaos')->first();

        // Acting as customer
        $this->actingAs($customer, 'customer');

        // 2. Customer saves a design
        $designData = [
            'id_produk' => $produk->id_produk,
            'file_desain' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'file_desain_belakang' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'lebar_cm' => 21,
            'tinggi_cm' => 29,
            'harga_desain' => 35000,
            'detail_sablon' => "1x A3 (25x35 cm) - Rp 35.000",
            'warna_baju' => '#ffffff',
        ];

        $response = $this->post(route('customer.designs.store'), $designData);
        $response->assertJson(['success' => true]);
        
        $desain = Desain::where('id_customer', $customer->id_customer)->first();
        $this->assertNotNull($desain);
        $this->assertEquals(35000, $desain->harga_desain);
        $this->assertEquals("1x A3 (25x35 cm) - Rp 35.000", $desain->detail_sablon);
        
        $cart = Cart::where('id_customer', $customer->id_customer)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(1, $cart->quantity);

        // 3. Customer checks out
        $checkoutData = [
            'cart_ids' => [$cart->id_cart],
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg')
        ];
        
        $response = $this->post(route('customer.checkout.store'), $checkoutData);
        $response->assertRedirect(route('customer.orders.index'));
        
        // 4. Assert Order Created
        $order = Order::where('id_customer', $customer->id_customer)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status_order);
        
        $totalHarga = ($produk->harga_dasar + 35000) * 1;
        $this->assertEquals($totalHarga, $order->total_harga);
        
        // Assert OrderDetail created
        $orderDetail = OrderDetail::where('id_order', $order->id_order)->first();
        $this->assertNotNull($orderDetail);
        $this->assertEquals(1, $orderDetail->quantity);
        $this->assertEquals($produk->harga_dasar, $orderDetail->harga_produk);
        $this->assertEquals(35000, $orderDetail->harga_desain);
        $this->assertEquals($totalHarga, $orderDetail->subtotal);
        
        // Assert Cart Cleared
        $this->assertDatabaseMissing('carts', [
            'id_cart' => $cart->id_cart
        ]);
    }
}
