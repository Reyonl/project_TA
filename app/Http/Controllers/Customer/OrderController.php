<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar semua pesanan milik customer yang sedang login.
     */
    public function index()
    {
        $orders = Order::where('id_customer', auth()->guard('customer')->id())
            ->orderBy('tanggal_order', 'desc')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail satu pesanan beserta item-itemnya.
     */
    public function show($id)
    {
        $order = Order::where('id_customer', auth()->guard('customer')->id())
            ->with(['orderDetails.produk', 'orderDetails.desain'])
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }
}
