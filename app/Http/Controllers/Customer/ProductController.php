<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Produk;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $allProduks = Produk::whereIn('jenis_produk', ['kaos', 'hoodie', 'polo'])->get();
        $grouped = $allProduks->groupBy(function($item) {
            $parts = explode(' ', $item->nama_produk);
            return ($parts[0] === 'Kaos') ? $parts[0] . ' ' . ($parts[1] ?? '') : $parts[0];
        });
        $produks = $grouped->map->first()->values();
        
        $activeOrdersCount = 0;
        $cartCount = 0;
        
        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();
            $activeOrdersCount = Order::where('id_customer', $customerId)
                                    ->whereIn('status_order', ['reviewing', 'pending_payment', 'processing'])
                                    ->count();
            $cartCount = Cart::where('id_customer', $customerId)->sum('quantity');
        }

        return view('customer.products.index', compact('produks', 'activeOrdersCount', 'cartCount'));
    }

    public function show(Produk $produk)
    {
        // Get variants belonging to the same group
        $parts = explode(' ', $produk->nama_produk);
        $groupName = ($parts[0] === 'Kaos') ? $parts[0] . ' ' . ($parts[1] ?? '') : $parts[0];
        $variants = Produk::where('nama_produk', 'like', $groupName . '%')->get();

        return view('customer.products.show', compact('produk', 'variants', 'groupName'));
    }
}
