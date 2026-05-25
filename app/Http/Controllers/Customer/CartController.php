<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_customer = Auth::guard('customer')->id();
        $carts = Cart::where('id_customer', $id_customer)
                    ->with(['produk', 'desain'])
                    ->latest()
                    ->get();
        return view('customer.cart.index', compact('carts'));
    }

    /**
     * Store a ready-made product directly to cart.
     */
    public function storeDirect(Request $request, Produk $produk)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            // color and size can be stored in a JSON column or note in the future if needed, 
            // but currently the Cart/OrderDetails table doesn't have size/color directly (it's in Desain).
            // For now, we will just add the product. If size is critical, we might need a migration for carts.
        ]);

        $id_customer = Auth::guard('customer')->id();

        Cart::create([
            'id_customer' => $id_customer,
            'id_produk' => $produk->id_produk,
            'id_desain' => null, // No custom design for ready-made
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('customer.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update the quantity of the cart item.
     */
    public function updateQuantity(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->id_customer !== Auth::guard('customer')->id()) {
            return abort(403, 'Unauthorized action.');
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        if ($cart->id_customer !== Auth::guard('customer')->id()) {
            return abort(403, 'Unauthorized action.');
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
