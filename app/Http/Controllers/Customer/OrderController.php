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

    /**
     * Upload Bukti Pembayaran untuk order tertentu.
     */
    public function uploadPayment(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format file harus JPEG, PNG, atau JPG.',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $order = Order::where('id_customer', auth()->guard('customer')->id())->findOrFail($id);

        if ($order->payment_status !== 'awaiting_payment' && $order->payment_status !== 'failed') {
            return back()->with('error', 'Pesanan ini tidak dapat diunggah bukti pembayarannya saat ini.');
        }

        $file = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('bukti_pembayaran', $filename, 'public');
        $buktiPath = 'bukti_pembayaran/' . $filename;

        $order->update([
            'bukti_pembayaran' => $buktiPath,
            'payment_status' => 'awaiting_verification',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari Admin.');
    }
}
