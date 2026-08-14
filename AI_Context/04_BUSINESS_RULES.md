# 04 — BUSINESS RULES

> **Source of Truth:** Implementasi aktual di Controller, FormRequest, Model, Blade  
> business_rules.txt lama digunakan sebagai referensi tambahan, bukan sumber utama.  
> Setiap rule ditandai sumbernya.

---

## 1. AUTENTIKASI & OTORISASI

### 1.1 Multi-Guard Authentication
- **[CONFIRMED - CheckRole.php, routes/web.php]** Ada 3 guard aktif:
  - `customer` → tabel `customers`
  - `admin` → tabel `admins`  
  - `web` (default Laravel) → tabel `users` (tidak digunakan untuk bisnis utama)

### 1.2 Role System
- **[CONFIRMED - Admin.php model, CheckRole.php]** Admin memiliki field `role` di tabel `admins`
- Nilai role yang dikenali middleware: `admin`, `owner`
- CheckRole middleware: `if (in_array($user->role, $roles))` — case sensitive

### 1.3 Akses Customer
- **[CONFIRMED - routes/web.php]** Semua route `/customer/*` memerlukan `auth:customer`
- Customer TIDAK bisa akses `/admin/*`
- Guest bisa akses: `/` (landing), `/katalog/{produk}`

### 1.4 Akses Admin
- **[CONFIRMED - routes/web.php]** Semua route `/admin/*` memerlukan `auth:admin`
- Route produk, template, order: tambahan `role:admin`
- Route laporan: `role:owner,admin` (owner DAN admin bisa akses)

### 1.5 Ownership Validation
- **[CONFIRMED - CartController]** Cart delete/update: validasi `$cart->id_customer !== Auth::guard('customer')->id()` → abort 403
- **[CONFIRMED - DesignController]** Design update: validasi `$desain->id_customer !== auth()->guard('customer')->id()` → abort 403
- **[CONFIRMED - DesignController@index]** Revisi: validasi `$desainRevisi->id_customer !== auth()->guard('customer')->id()` → abort 403
- **[CONFIRMED - CustomerOrderController]** Order show: query filter `where('id_customer', auth()->guard('customer')->id())`

---

## 2. PRODUK

### 2.1 Jenis Produk yang Valid (AKTUAL)
- **[CONFIRMED - Admin\ProductController@store validation]** Nilai valid: `kaos`, `hoodie`, `polo`
- ⚠️ Nilai `seragam` TIDAK ada dalam validasi admin meskipun ada referensi di canvas editor
- ⚠️ Nilai `topi` di-exclude dari landing page: `jenis_produk != 'topi'`

### 2.2 Tipe Produk
- **[CONFIRMED - Admin\ProductController]** Nilai valid: `kustom`, `jadi`
- Produk `jadi` = ready-made (tanpa desain custom)
- Produk `kustom` = perlu desain custom

### 2.3 Harga Produk
- **[CONFIRMED - CheckoutController]** Harga total per item: `(harga_dasar + harga_desain) * quantity`
- `harga_desain` diambil dari `Desain.harga_desain` (field di DB)
- Jika cart item tidak ada desain (`id_desain = null`): `harga_desain = 0`

### 2.4 Biaya Desain
- **[CONFIRMED - _editor_scripts.blade.php, recalculateTotalPrice]** Perhitungan harga per objek **DINONAKTIFKAN** (totalDesignPrice = 0 hardcoded)
- **[CONFIRMED - StoreDesainRequest]** Field `harga_desain` required, tipe numeric, min:0
- **[CONFIRMED - _editor_scripts.blade.php, payload]** Value yang dikirim: `window.currentHargaDesain || 0`
- Artinya: semua desain custom saat ini gratis (Rp 0)

---

## 3. DESAIN

### 3.1 Desain Wajib Depan
- **[CONFIRMED - StoreDesainRequest]** `file_desain` (front) required
- **[CONFIRMED - _editor_scripts.blade.php]** Validasi JS: `if(totalObjects === 0)` → SweetAlert warning "Kanvas Kosong"

### 3.2 Format File Desain
- **[CONFIRMED - _editor_scripts.blade.php, imageLoader]** Upload gambar: PNG, JPG, SVG (max tidak disebutkan di JS tapi ada pesan "Max 2MB")
- **[CONFIRMED - DesignController]** Server terima base64 PNG, decode dan simpan ke storage

### 3.3 Desain Sisi Lain
- **[CONFIRMED - StoreDesainRequest]** `file_desain_belakang`, `file_desain_kiri`, `file_desain_kanan` semua nullable
- Hanya dikirim jika canvas sisi tersebut ada objeknya

### 3.4 Simpan Canvas JSON
- **[CONFIRMED - migration 2026_08_08]** `canvas_front`, `canvas_back`, `canvas_left`, `canvas_right` disimpan sebagai longText
- Format: JSON string hasil `fabricCanvas.toJSON(['customType', 'sablonSize'])`
- Digunakan untuk load ulang editor saat revisi

### 3.5 Versioning Desain (Revisi)
- **[CONFIRMED - DesignController@update]** Saat revisi, BUAT desain BARU (bukan update record lama)
- Desain lama tetap ada, desain baru memiliki `parent_id = id_desain_lama`
- OrderDetail diupdate ke desain baru, status_desain direset ke 'pending'

### 3.6 Raw Assets
- **[CONFIRMED - DesignController, Desain.php model]** `raw_assets` disimpan sebagai JSON array di DB (di-cast ke array)
- Berisi path file gambar yang diupload customer ke canvas (bukan stiker/template)
- Disimpan di `storage/app/public/designs/assets/`

---

## 4. CART (KERANJANG)

### 4.1 Penambahan ke Cart
- **[CONFIRMED - DesignController@store]** Setelah desain disimpan, OTOMATIS dibuat cart item (quantity=1)
- **[CONFIRMED - CartController@storeDirect]** Produk ready-made bisa langsung ditambah ke cart tanpa desain

### 4.2 Update Quantity
- **[CONFIRMED - CartController@updateQuantity]** Validasi: integer, min:1
- Hanya owner cart yang bisa update (validasi manual)

### 4.3 Cart Tipe Produk Column
- ⚠️ **[DISCREPANCY]** Migration `2026_04_03_082027` menambahkan `tipe_proses` ke carts, tapi model Cart.php tidak memiliki field ini di `$fillable`. Status keberadaan kolom di DB tidak dikonfirmasi.

---

## 5. CHECKOUT

### 5.1 Syarat Checkout
- **[CONFIRMED - CheckoutController@index]** Memerlukan `cart_ids` sebagai array (query string)
- Cart ids harus milik customer yang login
- Cart tidak boleh kosong

### 5.2 Atomisitas Checkout
- **[CONFIRMED - CheckoutController@store]** Menggunakan `DB::transaction` untuk:
  - Buat Order
  - Buat OrderDetail untuk setiap cart item
  - Hapus cart items yang dicheckout

### 5.3 Upload Bukti Pembayaran
- **[CONFIRMED - CustomerOrderController@uploadPayment]**
- Hanya bisa upload jika `payment_status` = `awaiting_payment` ATAU `failed`
- Setelah upload: `payment_status` → `awaiting_verification`
- Validasi file: image, jpeg/png/jpg, max 2048KB
- Perlu dipanggil secara terpisah setelah checkout (NOT dalam proses checkout)

---

## 6. ORDER STATUS

### 6.1 Status Order (ENUM aktual, CONFIRMED migration)
```
reviewing         → default saat checkout baru dibuat
pending_payment   → setelah semua desain disetujui admin (AUTO)
processing        → setelah pembayaran diverifikasi admin (approve)
completed         → setelah pesanan selesai dikirim
cancelled         → dibatalkan admin
```

### 6.2 Auto-Transition Status Order
- **[CONFIRMED - AdminOrderController@updateStatusDesain]** Ketika admin menyetujui desain:
  - Cek apakah SEMUA order_details sudah `approved`
  - Jika ya DAN status order masih `reviewing`:
    - Update ke `pending_payment`
    - Update `payment_status` ke `awaiting_payment`

### 6.3 Status Pembayaran (ENUM aktual)
```
unpaid                → default saat order dibuat
awaiting_payment      → setelah semua desain approved
awaiting_verification → setelah customer upload bukti
paid                  → setelah admin approve pembayaran
failed                → setelah admin reject pembayaran
```

### 6.4 Status Desain per OrderDetail (ENUM aktual)
```
pending           → default saat order dibuat (jika ada desain)
approved          → admin menyetujui desain
revision_required → admin meminta revisi
```
Produk ready-made (tanpa desain): default `disetujui` (lihat Discrepancy #4 di 12_DISCREPANCIES.md)

---

## 7. VALIDASI ADMIN

### 7.1 Status Order yang Boleh Diset Admin
- **[CONFIRMED - AdminOrderController@updateStatus]** Nilai valid: `reviewing`, `pending_payment`, `processing`, `completed`, `cancelled`
- Tidak ada validasi urutan status (admin bisa set status apapun)

### 7.2 Status Desain yang Boleh Diset Admin
- **[CONFIRMED - AdminOrderController@updateStatusDesain]** Nilai valid: `approved`, `revision_required`

---

## 8. LAPORAN OWNER

### 8.1 Filter Tanggal
- **[CONFIRMED - ReportController]** Default: 30 hari ke belakang
- Bisa diubah via `?start_date=&end_date=`

### 8.2 Kalkulasi Revenue
- **[CONFIRMED - ReportController]** Revenue menggunakan `status_order = 'selesai'`
- ⚠️ **BUG:** Enum aktual adalah `'completed'`, bukan `'selesai'`. Revenue selalu 0.

### 8.3 Export CSV
- **[CONFIRMED - ReportController@exportCsv]** Invoice ID format: `INV-2026XXXXX`
- Header CSV: ID Invoice, Tanggal, Nama Customer, Item Dibeli, Total Harga (Rp), Status Pembayaran
