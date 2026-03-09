<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Produk;
use App\Models\Desain;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Parameter id_produk dan id_desain diterima dari alur sebelumnya
        $produk = Produk::find($request->id_produk);
        $desain = Desain::find($request->id_desain);
        return view('customer.checkout.index', compact('produk', 'desain'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_desain' => 'required|exists:desains,id_desain',
            'quantity' => 'required|integer|min:1'
        ]);

        $produk = Produk::findOrFail($request->id_produk);
        $desain = Desain::findOrFail($request->id_desain);

        $subtotal = ($produk->harga_dasar + $desain->harga_desain) * $request->quantity;

        $order = Order::create([
            'id_customer' => auth()->guard('customer')->id(),
            'tanggal_order' => now(),
            'status_order' => 'pending',
            'total_harga' => $subtotal,
        ]);

        OrderDetail::create([
            'id_order' => $order->id_order,
            'id_produk' => $produk->id_produk,
            'id_desain' => $desain->id_desain,
            'quantity' => $request->quantity,
            'harga_produk' => $produk->harga_dasar,
            'harga_desain' => $desain->harga_desain,
            'subtotal' => $subtotal,
        ]);

        return redirect()->route('customer.orders.index')->with('success', 'Pesanan sablon Anda berhasil dibuat! Tim kami akan segera memprosesnya.');
    }
}
