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

        $startStr = $startDate->toDateString(); // Y-m-d
        $endStr = $endDate->toDateString(); // Y-m-d
        $startDateTimeStr = $startDate->toDateTimeString(); // Y-m-d H:i:s
        $endDateTimeStr = $endDate->toDateTimeString(); // Y-m-d H:i:s

        // --- MANAJEMEN GROWTH METRICS ---
        $diffDays = $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;

        $previousEnd = (clone $startDate)->subSecond();
        $previousStart = (clone $previousEnd)->subDays($diffDays)->startOfDay();
        
        $prevStartStr = $previousStart->toDateString();
        $prevEndStr = $previousEnd->toDateString();
        $prevStartDateTimeStr = $previousStart->toDateTimeString();
        $prevEndDateTimeStr = $previousEnd->toDateTimeString();

        // Data Dinamis berdasarkan Tanggal (Current)
        $totalOrders = Order::whereBetween('tanggal_order', [$startStr, $endStr])->count();
        $totalRevenue = Order::where('status_order', 'selesai')
                             ->whereBetween('tanggal_order', [$startStr, $endStr])
                             ->sum('total_harga');
        $totalDesains = Desain::whereBetween('created_at', [$startDateTimeStr, $endDateTimeStr])->count();

        // Data Dinamis berdasarkan Tanggal (Previous)
        $prevOrders = Order::whereBetween('tanggal_order', [$prevStartStr, $prevEndStr])->count();
        $prevRevenue = Order::where('status_order', 'selesai')
                            ->whereBetween('tanggal_order', [$prevStartStr, $prevEndStr])
                            ->sum('total_harga');
        $prevDesains = Desain::whereBetween('created_at', [$prevStartDateTimeStr, $prevEndDateTimeStr])->count();

        $growthOrders = $prevOrders ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1) : ($totalOrders > 0 ? 100 : 0);
        $growthRevenue = $prevRevenue ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : ($totalRevenue > 0 ? 100 : 0);
        $growthDesains = $prevDesains ? round((($totalDesains - $prevDesains) / $prevDesains) * 100, 1) : ($totalDesains > 0 ? 100 : 0);

        // Data untuk Grafik Status Pesanan (Doughnut Chart)
        $statusCounts = Order::select('status_order', \DB::raw('count(*) as total'))
                            ->whereBetween('tanggal_order', [$startStr, $endStr])
                            ->groupBy('status_order')
                            ->pluck('total', 'status_order')
                            ->toArray();

        // Data untuk Grafik Pendapatan Bulanan (Bar Chart) - STRICT IKUT FILTER
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

        // Data Seluruh Transaksi (Bukan lagi limit 10) untuk DataTables 
        $allOrders = Order::with(['customer', 'orderDetails.produk'])
                            ->whereBetween('tanggal_order', [$startStr, $endStr])
                            ->orderBy('tanggal_order', 'desc')
                            ->get();

        // --- MANAJEMEN EXPORT EXCEL (CSV) ---
        if ($request->has('export_csv')) {
            return $this->exportCsv($allOrders, $startStr, $endStr);
        }

        return view('owner.reports.index', compact(
            'totalOrders', 'totalRevenue', 'totalDesains', 
            'growthOrders', 'growthRevenue', 'growthDesains',
            'statusCounts', 'formattedMonthly', 'produkPopuler', 'warnaPopuler',
            'startDate', 'endDate', 'allOrders'
        ));
    }

    private function exportCsv($orders, $start, $end)
    {
        $filename = "Laporan_Penjualan_DailyCo_{$start}_to_{$end}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Invoice', 'Tanggal', 'Nama Customer', 'Item Dibeli', 'Total Harga (Rp)', 'Status Pembayaran'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $invoiceId = 'INV-2026' . str_pad($order->id_order, 5, '0', STR_PAD_LEFT);
                $customerName = $order->customer->nama_customer ?? 'Umum';
                
                $items = $order->orderDetails->map(function($od) {
                    return ($od->produk->nama_produk ?? 'Custom') . ' (x' . $od->quantity . ')';
                })->implode(', ');

                $row = [
                    $invoiceId,
                    \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d H:i'),
                    $customerName,
                    $items,
                    $order->total_harga,
                    strtoupper(str_replace('_', ' ', $order->status_order))
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
