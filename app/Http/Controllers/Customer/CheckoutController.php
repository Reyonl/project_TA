<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function index()
    {
        $id_customer = Auth::guard('customer')->id();
        $carts = Cart::where('id_customer', $id_customer)->with(['produk', 'desain'])->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += ($cart->produk->harga_dasar + $cart->desain->harga_desain) * $cart->quantity;
        }

        return view('customer.checkout.index', compact('carts', 'totalHarga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $id_customer = Auth::guard('customer')->id();
        $carts = Cart::where('id_customer', $id_customer)->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Hitung total harga
        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += ($cart->produk->harga_dasar + $cart->desain->harga_desain) * $cart->quantity;
        }

        // Upload bukti pembayaran
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/bukti_pembayaran', $filename);
            $buktiPath = 'bukti_pembayaran/' . $filename;
        }

        // Buat Order Induk
        $order = Order::create([
            'id_customer' => $id_customer,
            'tanggal_order' => now(),
            'status_order' => 'pending',
            'total_harga' => $totalHarga,
            'bukti_pembayaran' => $buktiPath,
        ]);

        // Buat Order Details
        foreach ($carts as $cart) {
            $subtotalDetail = ($cart->produk->harga_dasar + $cart->desain->harga_desain) * $cart->quantity;
            
            OrderDetail::create([
                'id_order' => $order->id_order,
                'id_produk' => $cart->id_produk,
                'id_desain' => $cart->id_desain,
                'quantity' => $cart->quantity,
                'harga_produk' => $cart->produk->harga_dasar,
                'harga_desain' => $cart->desain->harga_desain,
                'subtotal' => $subtotalDetail,
                'status_desain' => 'pending',
            ]);
        }

        // Kosongkan keranjang
        Cart::where('id_customer', $id_customer)->delete();

        return redirect()->route('customer.orders.index')->with('success', 'Pesanan sablon Anda berhasil dibuat! Tim kami sedang memverifikasi pembayaran Anda.');
    }
}
