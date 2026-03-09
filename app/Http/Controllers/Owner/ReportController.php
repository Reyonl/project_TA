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
        
        return view('owner.reports.index', compact('totalOrders', 'totalRevenue', 'totalDesains'));
    }
}
