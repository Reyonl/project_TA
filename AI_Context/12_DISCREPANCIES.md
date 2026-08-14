# 12 — DISCREPANCIES

> **Definisi:** Ketidaksesuaian antara implementasi aktual dan dokumentasi lama, komentar code, atau asumsi yang mungkin dimiliki developer baru.  
> **Prioritas penanganan:** HIGH (berpotensi menyebabkan bug/kerusakan) > MEDIUM > LOW (informasional)

---

## DISCREPANCY #1 — Revenue Report Bug ✅ FIXED

**Lokasi:** `app/Http/Controllers/Owner/ReportController.php`  
**Severity:** HIGH — data laporan tidak akurat  
**Status:** ✅ FIXED (2026-08-11)

**Root Cause:**
Nilai enum `'selesai'` adalah enum lama (sebelum migration `2026_08_07_233031`). Migration tersebut mengubah semua `status_order = 'selesai'` menjadi `'completed'` di data, dan mengubah enum DB. ReportController tidak diperbarui mengikuti perubahan ini.

**Fix yang dilakukan:**
```diff
// ReportController.php baris 39-41 (totalRevenue)
- ->where('status_order', 'selesai')
+ ->where('status_order', 'completed')

// ReportController.php baris 46-48 (prevRevenue)
- ->where('status_order', 'selesai')
+ ->where('status_order', 'completed')

// ReportController.php baris 67 (monthlyRevenue)
- ->where('status_order', 'selesai')
+ ->where('status_order', 'completed')
```

---

## DISCREPANCY #2 — Ready-Made Order Detail Status ✅ FIXED

**Lokasi:** `app/Http/Controllers/Customer/CheckoutController.php`  
**Severity:** HIGH — potensi bug saat admin review  
**Status:** ✅ FIXED (2026-08-11)

**Root Cause:**
Nilai `'disetujui'` adalah nilai enum lama yang digunakan sebelum migration `2026_08_07_233031`. Migration tersebut mengubah semua `status_desain = 'disetujui'` menjadi `'approved'` di data dan memperbarui enum DB. CheckoutController tidak diperbarui mengikuti perubahan ini.

**Fix yang dilakukan:**
```diff
// CheckoutController.php baris 92
- 'status_desain' => $cart->id_desain ? 'pending' : 'disetujui',
+ 'status_desain' => $cart->id_desain ? 'pending' : 'approved', // Ready-made: design already approved
```

---

## DISCREPANCY #3 — payment_status Tidak Ada di Order $fillable ✅ FIXED

**Lokasi:** `app/Models/Order.php`  
**Severity:** MEDIUM — berpotensi menyebabkan `payment_status` tidak terisi via mass assignment  
**Status:** ✅ FIXED (2026-08-11)

**Root Cause:**
Field `payment_status` ditambahkan oleh migration `2026_08_07_233031` ke tabel `orders`, tetapi `Order::$fillable` tidak diperbarui. Akibatnya `Order::create(['payment_status' => 'unpaid', ...])` di `CheckoutController` silently drop field `payment_status` karena mass assignment protection.

**Catatan:** Nilai DB default untuk `payment_status` adalah `'unpaid'` (dari migration), sehingga nilai yang ingin diisi sama dengan default — bug ini tidak menyebabkan kesalahan data di tahap ini, tapi merupakan ketidakhandalan yang harus diperbaiki dan akan bermasalah jika nilai non-default perlu di-create.

**Fix yang dilakukan:**
```diff
// app/Models/Order.php
 protected $fillable = [
     'id_customer',
     'tanggal_order',
     'status_order',
+    'payment_status',
     'total_harga',
     'bukti_pembayaran',
 ];
```

---

## DISCREPANCY #4 — Jenis Produk 'seragam' (LOW-MEDIUM)

**Lokasi:** `resources/views/customer/designs/_editor_scripts.blade.php`, `_canvas_editor.blade.php`  
**Severity:** MEDIUM — tidak konsisten  

**Masalah:**
```php
// Di canvas editor (_editor_scripts.blade.php):
$mockupBase = match($produk->jenis_produk) {
    'kaos' => ...,
    'hoodie' => ...,
    'polo' => ...,
    'seragam' => 'seragam',    // ADA di canvas editor
    default => 'kaos'
};

// Di Admin\ProductController validasi:
'jenis_produk' => 'required|in:kaos,hoodie,polo',  // 'seragam' TIDAK ADA
```

**Dampak:** Produk dengan `jenis_produk = 'seragam'` tidak bisa dibuat oleh admin (validasi gagal). Tapi canvas editor sudah siap menanganinya. Ini kemungkinan fitur yang dihapus tapi kode lama belum dibersihkan.

**Fix:** Pilih salah satu:
1. Hapus 'seragam' dari canvas editor (jika tidak akan diimplementasikan)
2. Tambahkan 'seragam' ke validasi admin (jika akan diimplementasikan)

---

## DISCREPANCY #5 — Debug Route Masih Aktif (MEDIUM)

**Lokasi:** `routes/web.php`  
**Severity:** MEDIUM — security concern  

**Masalah:**
```php
Route::get('/debug-storage', function() { ... });
```

Route `/debug-storage` masih aktif dan dapat diakses tanpa autentikasi.

**Dampak:** Bisa expose informasi storage structure dan file paths ke siapapun.

**Fix:** Hapus route ini sebelum deploy ke production, atau wrap dengan `auth:admin` dan `role:admin`.

---

## DISCREPANCY #6 — Cart `tipe_proses` Column (LOW)

**Lokasi:** `database/migrations/2026_04_03_082027_add_tipe_proses_to_carts_table.php`, `app/Models/Cart.php`  
**Severity:** LOW — inkonsistensi minor  

**Masalah:**
Migration menambahkan `tipe_proses` ke tabel `carts`, tapi kolom ini tidak ada di `Cart::$fillable`.

**Dampak:** Kolom mungkin ada di DB tapi tidak bisa diisi via `Cart::create()`. Tidak ada fungsionalitas yang rusak karena tidak ada kode yang menggunakannya.

**Perlu Dikonfirmasi:** Apakah migration ini sudah dijalankan dan kolom ada di DB? Apakah ada migration yang menghapusnya kemudian?

---

## DISCREPANCY #7 — Versi TailwindCSS Ganda (LOW)

**Lokasi:** `package.json`  
**Severity:** LOW — build tools confusion  

**Masalah:**
```json
"dependencies": {
    "@tailwindcss/vite": "^4.1.11"  // TailwindCSS v4
},
"devDependencies": {
    "tailwindcss": "^3.1.0"   // TailwindCSS v3 (berbeda versi!)
}
```

**Dampak:** Tidak jelas versi mana yang aktif digunakan. TailwindCSS v4 memiliki API yang berbeda dari v3. Kemungkinan menyebabkan konflik build.

---

## DISCREPANCY #8 — Auth Route Logout Tanpa Middleware (INFO)

**Lokasi:** `routes/auth.php`  
**Severity:** LOW — ini disengaja berdasarkan komentar di kode  

**Situasi:**
```php
// Komentar aktual di kode:
// "Logout route tanpa middleware 'auth' agar bisa diakses oleh semua guard (admin, customer, web)"
Route::post('logout', ...)->name('logout');
```

Ini adalah desain yang disengaja untuk mendukung multi-guard. Bukan bug.

---

## DISCREPANCY #9 — User Model & Tabel (INFO)

**Lokasi:** `app/Models/User.php`  
**Severity:** LOW  

Tabel `users` telah dihapus dalam migration (2026-08-01), tapi `User.php` model masih ada karena dibutuhkan paket Laravel Fortify dan Breeze untuk compile. Tidak ada fungsionalitas bisnis yang menggunakan model ini.

---

## DISCREPANCY #10 — Settings Routes Menggunakan Guard 'web' (INFO)

**Lokasi:** `routes/settings.php`  
**Severity:** LOW — kemungkinan legacy dari Breeze starter kit  

Settings routes (`/settings/profile`) menggunakan `middleware('auth')` (guard `web` default), bukan `auth:customer`. Customer tidak memiliki akses ke settings ini, dan tidak ada link ke settings dari area customer.

---

## Ringkasan Prioritas Fix

| # | Masalah | Severity | Estimasi Fix |
|---|---|---|---|
| 1 | Revenue bug (`'selesai'` vs `'completed'`) | 🔴 HIGH | 1 baris |
| 2 | Ready-made status (`'disetujui'` vs `'approved'`) | 🔴 HIGH | 1 baris |
| 3 | `payment_status` tidak di $fillable | 🟡 MEDIUM | 1 baris (perlu verifikasi) |
| 4 | Jenis produk 'seragam' inkonsisten | 🟡 MEDIUM | Keputusan fitur |
| 5 | Debug route aktif | 🟡 MEDIUM | Hapus 1 route |
| 6 | Cart `tipe_proses` orphan column | 🟢 LOW | Dokumentasi saja |
| 7 | TailwindCSS versi ganda | 🟢 LOW | Build tool cleanup |
| 8-10 | Lainnya | ℹ️ INFO | - |
