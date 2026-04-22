<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\Desain;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default filter ke 30 hari ke belakang jika tidak ada request tanggal
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // Gunakan string spesifik untuk query DB menghindari bug tipe data DATE vs DATETIME
        $startStr = $startDate->toDateString(); // Y-m-d
        $endStr = $endDate->toDateString(); // Y-m-d

        $startDateTimeStr = $startDate->toDateTimeString(); // Y-m-d H:i:s
        $endDateTimeStr = $endDate->toDateTimeString(); // Y-m-d H:i:s

        // Data Dinamis berdasarkan Tanggal
        $totalOrders = Order::whereBetween('tanggal_order', [$startStr, $endStr])->count();
        $totalRevenue = Order::where('status_order', 'selesai')
                             ->whereBetween('tanggal_order', [$startStr, $endStr])
                             ->sum('total_harga');
        
        $totalDesains = Desain::whereBetween('created_at', [$startDateTimeStr, $endDateTimeStr])->count();
        
        // Data untuk Grafik Status Pesanan (Doughnut Chart)
        $statusCounts = Order::select('status_order', \DB::raw('count(*) as total'))
                            ->whereBetween('tanggal_order', [$startStr, $endStr])
                            ->groupBy('status_order')
                            ->pluck('total', 'status_order')
                            ->toArray();

        // Data untuk Grafik Pendapatan Bulanan (Bar Chart) - SEKARANG STRICT IKUT FILTER
        $monthlyRevenue = Order::select(
                                \DB::raw('SUM(total_harga) as revenue'),
                                \DB::raw('MONTH(tanggal_order) as month')
                            )
                            ->where('status_order', 'selesai')
                            ->whereBetween('tanggal_order', [$startStr, $endStr])
                            ->groupBy('month')
                            ->orderBy('month')
                            ->pluck('revenue', 'month')
                            ->toArray();

        // Siapkan array dengan bulan 1-12
        $formattedMonthly = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedMonthly[$i] = $monthlyRevenue[$i] ?? 0;
        }

        // Data Produk Terpopuler berdasarkan rentang filter
        $produkPopuler = \App\Models\OrderDetail::join('produks', 'order_details.id_produk', '=', 'produks.id_produk')
                            ->join('orders', 'order_details.id_order', '=', 'orders.id_order')
                            ->select('produks.nama_produk', \DB::raw('SUM(order_details.quantity) as total_terjual'))
                            ->whereBetween('orders.tanggal_order', [$startStr, $endStr])
                            ->groupBy('produks.id_produk', 'produks.nama_produk')
                            ->orderByDesc('total_terjual')
                            ->limit(5)
                            ->get();

        // Data Warna Baju Paling Diminati
        $warnaPopuler = \App\Models\Desain::select('warna_baju', \DB::raw('count(*) as total'))
                            ->whereNotNull('warna_baju')
                            ->whereBetween('created_at', [$startDateTimeStr, $endDateTimeStr])
                            ->groupBy('warna_baju')
                            ->orderByDesc('total')
                            ->limit(5)
                            ->get();

        // Data Riwayat Transaksi Terbaru (Max 10 baris untuk Tabel Visualisasi Owner)
        $recentOrders = Order::with('customer')
                            ->whereBetween('tanggal_order', [$startStr, $endStr])
                            ->orderBy('tanggal_order', 'desc')
                            ->limit(10)
                            ->get();

        return view('owner.reports.index', compact(
            'totalOrders', 'totalRevenue', 'totalDesains', 
            'statusCounts', 'formattedMonthly', 'produkPopuler', 'warnaPopuler',
            'startDate', 'endDate', 'recentOrders'
        ));
    }
}
