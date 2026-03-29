<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
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
