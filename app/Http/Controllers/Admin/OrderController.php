<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;

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
        $request->validate(['status_order' => 'required|in:reviewing,pending_payment,processing,completed,cancelled']);
        $order->update(['status_order' => $request->status_order]);
        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function updateStatusDesain(Request $request, Order $order, OrderDetail $orderDetail)
    {
        $request->validate([
            'status_desain' => 'required|in:approved,revision_required',
            'catatan_admin' => 'nullable|string',
        ]);

        $orderDetail->update([
            'status_desain' => $request->status_desain,
            'catatan_admin' => $request->catatan_admin,
        ]);

        // Auto-update order status to pending_payment if ALL details are approved
        $allApproved = true;
        foreach ($order->orderDetails as $od) {
            if ($od->id_order_detail !== $orderDetail->id_order_detail && $od->status_desain !== 'approved') {
                $allApproved = false;
                break;
            }
        }

        if ($allApproved && $request->status_desain === 'approved' && $order->status_order === 'reviewing') {
            $order->update([
                'status_order' => 'pending_payment',
                'payment_status' => 'awaiting_payment'
            ]);
            return back()->with('success', 'Desain disetujui. Pesanan otomatis diteruskan ke tahap pembayaran.');
        }

        return back()->with('success', 'Status & catatan desain diperbarui.');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $request->validate(['action' => 'required|in:approve,reject']);

        if ($request->action === 'approve') {
            $order->update([
                'payment_status' => 'paid',
                'status_order' => 'processing'
            ]);
            return back()->with('success', 'Pembayaran berhasil diverifikasi. Pesanan masuk ke tahap produksi.');
        } else {
            $order->update([
                'payment_status' => 'failed',
                'status_order' => 'pending_payment' // Kick back to payment
            ]);
            return back()->with('success', 'Pembayaran ditolak. Pelanggan akan diminta mengunggah ulang bukti bayar.');
        }
    }
}
