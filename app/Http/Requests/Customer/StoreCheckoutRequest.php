<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi request untuk proses checkout (pembayaran).
 * Digunakan oleh CheckoutController@store.
 */
class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    public function rules(): array
    {
        return [
            'cart_ids' => 'required|array|min:1',
            'cart_ids.*' => 'exists:carts,id_cart',
        ];
    }

    public function messages(): array
    {
        return [
            'cart_ids.required' => 'Keranjang tidak boleh kosong.',
            'cart_ids.min' => 'Pilih minimal 1 item untuk checkout.',
        ];
    }
}
