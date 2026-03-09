<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer', 'orderDetails.desain')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('orderDetails.desain', 'orderDetails.produk', 'customer');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status_order' => 'required|in:pending,diproses,selesai,dibatalkan']);
        $order->update(['status_order' => $request->status_order]);
        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
