<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Produk;

class ProductController extends Controller
{
    public function index()
    {
        $produks = Produk::all();
        return view('customer.products.index', compact('produks'));
    }

    public function show(Produk $produk)
    {
        return view('customer.products.show', compact('produk'));
    }
}
