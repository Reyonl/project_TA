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
        $produks = Produk::all();
        
        $activeOrdersCount = 0;
        $cartCount = 0;
        
        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();
            $activeOrdersCount = Order::where('id_customer', $customerId)
                                    ->whereIn('status_order', ['pending', 'diproses'])
                                    ->count();
            $cartCount = Cart::where('id_customer', $customerId)->sum('quantity');
        }

        return view('customer.products.index', compact('produks', 'activeOrdersCount', 'cartCount'));
    }

    public function show(Produk $produk)
    {
        return view('customer.products.show', compact('produk'));
    }
}
