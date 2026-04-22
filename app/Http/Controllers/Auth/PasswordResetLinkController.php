<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'phone_last_3' => ['required', 'string', 'size:3'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $customer = \App\Models\Customer::where('email', $request->email)->first();

        // Cek fallback jika itu adalah admin, kita tidak beri akses reset super simple
        if (!$customer) {
            $admin = \App\Models\Admin::where('email', $request->email)->first();
            if ($admin) {
                return back()->withErrors(['email' => 'Reset kata sandi Admin hanya dapat dilakukan lewat Administrator Database.']);
            }

            return back()->withInput($request->only('email', 'phone_last_3'))
                         ->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
        }

        // Cek kecocokan 3 digit terakhir NO HP
        // Hapus karakter selain angka untuk pastikan perbandingan akurat
        $hp_bersih = preg_replace('/[^0-9]/', '', $customer->no_hp);
        $tiga_digit_akhir_db = substr($hp_bersih, -3);

        if ($tiga_digit_akhir_db !== $request->phone_last_3) {
            return back()->withInput($request->only('email', 'phone_last_3'))
                         ->withErrors(['phone_last_3' => '3 Digit terakhir nomor HP tidak cocok dengan data terdaftar.']);
        }

        // Jika semua cocok, perbarui password langsung
        $customer->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $customer->save();

        return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah! Silakan login dengan kata sandi baru Anda.');
    }
}
