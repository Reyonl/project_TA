<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi request untuk menyimpan desain baru dari Design Editor.
 * Digunakan oleh DesignController@store dan @update.
 */
class StoreDesainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    public function rules(): array
    {
        return [
            'id_produk' => 'required|exists:produks,id_produk',
            'file_desain' => 'required|string',
            'lebar_cm' => 'required|numeric|min:1',
            'tinggi_cm' => 'required|numeric|min:1',
            'file_desain_belakang' => 'nullable|string',
            'lebar_cm_belakang' => 'nullable|numeric|min:1',
            'tinggi_cm_belakang' => 'nullable|numeric|min:1',
            'file_desain_kiri' => 'nullable|string',
            'lebar_cm_kiri' => 'nullable|numeric|min:1',
            'tinggi_cm_kiri' => 'nullable|numeric|min:1',
            'file_desain_kanan' => 'nullable|string',
            'lebar_cm_kanan' => 'nullable|numeric|min:1',
            'tinggi_cm_kanan' => 'nullable|numeric|min:1',
            'warna_baju' => 'nullable|string|max:20',
            'raw_assets' => 'nullable|array',
            'harga_desain' => 'required|numeric|min:0',
            'detail_sablon' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_produk.required' => 'Produk harus dipilih.',
            'id_produk.exists' => 'Produk yang dipilih tidak valid.',
            'file_desain.required' => 'Desain bagian depan wajib diisi.',
            'lebar_cm.required' => 'Lebar desain wajib diisi.',
            'tinggi_cm.required' => 'Tinggi desain wajib diisi.',
        ];
    }
}
