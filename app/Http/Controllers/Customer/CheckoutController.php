<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCheckoutRequest;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $id_customer = Auth::guard('customer')->id();
        $cartIds = $request->query('cart_ids');

        if (!$cartIds || !is_array($cartIds)) {
            return redirect()->route('customer.cart.index')->with('error', 'Silakan pilih minimal 1 item untuk checkout.');
        }

        $carts = Cart::where('id_customer', $id_customer)
            ->whereIn('id_cart', $cartIds)
            ->with(['produk', 'desain'])
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong atau item tidak ditemukan.');
        }

        $totalHarga = 0;
        foreach ($carts as $cart) {
            $hargaDesain = $cart->desain ? $cart->desain->harga_desain : 0;
            $totalHarga += ($cart->produk->harga_dasar + $hargaDesain) * $cart->quantity;
        }

        return view('customer.checkout.index', compact('carts', 'totalHarga'));
    }

    public function store(StoreCheckoutRequest $request)
    {
        $id_customer = Auth::guard('customer')->id();
        $cartIds = $request->input('cart_ids');

        if (!$cartIds || !is_array($cartIds)) {
            return redirect()->route('customer.cart.index')->with('error', 'Silakan pilih minimal 1 item untuk checkout.');
        }

        $carts = Cart::where('id_customer', $id_customer)->whereIn('id_cart', $cartIds)->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Hitung total harga
        $totalHarga = 0;
        foreach ($carts as $cart) {
            $hargaDesain = $cart->desain ? $cart->desain->harga_desain : 0;
            $totalHarga += ($cart->produk->harga_dasar + $hargaDesain) * $cart->quantity;
        }

        // Gunakan transaction agar order & detail atomik
        $order = DB::transaction(function () use ($id_customer, $totalHarga, $carts, $cartIds) {
            // Buat Order Induk
            $order = Order::create([
                'id_customer' => $id_customer,
                'tanggal_order' => now(),
                'status_order' => 'reviewing',
                'payment_status' => 'unpaid',
                'total_harga' => $totalHarga,
                'bukti_pembayaran' => null,
            ]);

            // Buat Order Details
            foreach ($carts as $cart) {
                $hargaDesain = $cart->desain ? $cart->desain->harga_desain : 0;
                $subtotalDetail = ($cart->produk->harga_dasar + $hargaDesain) * $cart->quantity;
                
                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'id_produk' => $cart->id_produk,
                    'id_desain' => $cart->id_desain, // Will be null for ready-made
                    'quantity' => $cart->quantity,
                    'harga_produk' => $cart->produk->harga_dasar,
                    'harga_desain' => $hargaDesain,
                    'subtotal' => $subtotalDetail,
                    'status_desain' => $cart->id_desain ? 'pending' : 'approved', // Ready-made: design already approved
                ]);
            }

            // Kosongkan keranjang (hanya item yang dicheckout)
            Cart::where('id_customer', $id_customer)->whereIn('id_cart', $cartIds)->delete();

            return $order;
        });

        return redirect()->route('customer.orders.index')->with('success', 'Pesanan Anda berhasil dibuat dan desain sedang menunggu verifikasi admin. Silakan pantau status pesanan Anda di sini.');
    }
}
