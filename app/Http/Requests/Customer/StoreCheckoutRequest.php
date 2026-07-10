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
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cart_ids' => 'required|array|min:1',
            'cart_ids.*' => 'exists:carts,id_cart',
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format file harus JPEG, PNG, atau JPG.',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}
