<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Produk;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard admin.
     */
    public function index()
    {
        // Jika owner (dari middleware atau pengecekan), redirect ke laporan
        if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->role == 'owner') {
            return redirect()->route('admin.report.index');
        }

        // Statistik
        $pesananBaru = Order::whereIn('status_order', ['reviewing', 'pending_payment'])->count();
        $sedangDiproses = Order::where('status_order', 'processing')->count();
        $totalProduk = Produk::count();

        // 5 Pesanan Terbaru
        $recentOrders = Order::with('customer')
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        return view('admin.dashboard', compact(
            'pesananBaru', 
            'sedangDiproses', 
            'totalProduk', 
            'recentOrders'
        ));
    }
}
