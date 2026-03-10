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

        return view('owner.reports.index', compact('totalOrders', 'totalRevenue', 'totalDesains', 'statusCounts', 'formattedMonthly'));
    }
}
