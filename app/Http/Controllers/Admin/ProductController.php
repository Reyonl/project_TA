<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $produks = Produk::latest()->get();
        return view('admin.products.index', compact('produks'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_produk' => 'required|in:kaos,hoodie,polo',
            'tipe_produk' => 'required|in:kustom,jadi',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ukuran_tersedia' => 'nullable|array',
            'ukuran_tersedia.*' => 'string|in:XS,S,M,L,XL,2XL,3XL,4XL'
        ]);

        $data = $request->except(['gambar_produk', 'ukuran_tersedia']);
        $data['ukuran_tersedia'] = json_encode($request->input('ukuran_tersedia', []));

        if ($request->hasFile('gambar_produk')) {
            $data['gambar_produk'] = $request->file('gambar_produk')->store('products', 'public');
        }

        Produk::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Produk $product)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_produk' => 'required|in:kaos,hoodie,polo',
            'tipe_produk' => 'required|in:kustom,jadi',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ukuran_tersedia' => 'nullable|array',
            'ukuran_tersedia.*' => 'string|in:XS,S,M,L,XL,2XL,3XL,4XL'
        ]);

        $data = $request->except(['gambar_produk', 'ukuran_tersedia']);
        $data['ukuran_tersedia'] = json_encode($request->input('ukuran_tersedia', []));

        if ($request->hasFile('gambar_produk')) {
            if ($product->gambar_produk) {
                Storage::disk('public')->delete($product->gambar_produk);
            }
            $data['gambar_produk'] = $request->file('gambar_produk')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $product)
    {
        if ($product->gambar_produk) {
            Storage::disk('public')->delete($product->gambar_produk);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
