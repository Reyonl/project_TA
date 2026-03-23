<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Desain;

class ReportController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status_order', 'selesai')->sum('total_harga');
        $totalDesains = Desain::count();
        
        // Data untuk Grafik Status Pesanan (Doughnut Chart)
        $statusCounts = Order::select('status_order', \DB::raw('count(*) as total'))
                            ->groupBy('status_order')
                            ->pluck('total', 'status_order')
                            ->toArray();

        // Data untuk Grafik Pendapatan Bulanan (Bar Chart) - 6 bulan terakhir
        $monthlyRevenue = Order::select(
                                \DB::raw('SUM(total_harga) as revenue'),
                                \DB::raw('MONTH(tanggal_order) as month')
                            )
                            ->where('status_order', 'selesai')
                            ->where('tanggal_order', '>=', now()->subMonths(6))
                            ->groupBy('month')
                            ->orderBy('month')
                            ->pluck('revenue', 'month')
                            ->toArray();

        // Siapkan array dengan bulan 1-12
        $formattedMonthly = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedMonthly[$i] = $monthlyRevenue[$i] ?? 0;
        }

        // Data Produk Terpopuler
        $produkPopuler = \App\Models\OrderDetail::join('produks', 'order_details.id_produk', '=', 'produks.id_produk')
                            ->select('produks.nama_produk', \DB::raw('SUM(order_details.quantity) as total_terjual'))
                            ->groupBy('produks.id_produk', 'produks.nama_produk')
                            ->orderByDesc('total_terjual')
                            ->limit(5)
                            ->get();

        // Data Warna Baju Paling Diminati
        $warnaPopuler = \App\Models\Desain::select('warna_baju', \DB::raw('count(*) as total'))
                            ->whereNotNull('warna_baju')
                            ->groupBy('warna_baju')
                            ->orderByDesc('total')
                            ->limit(5)
                            ->get();

        return view('owner.reports.index', compact(
            'totalOrders', 'totalRevenue', 'totalDesains', 
            'statusCounts', 'formattedMonthly', 'produkPopuler', 'warnaPopuler'
        ));
    }
}
