# 03 — BUSINESS FLOW

> **Source of Truth:** Controller dan Blade views aktual  
> Flow berikut diambil dari implementasi aktual, bukan diagram lama.

---

## FLOW CUSTOMER

### 1. Registrasi
```
GET  /register → RegisteredUserController@create
POST /register → RegisteredUserController@store
  - Validasi nama, email, password
  - Buat record di tabel `customers`
  - Auto-login dengan guard 'customer'
  - Redirect ke /customer/dashboard
```

### 2. Login
```
GET  /login → AuthenticatedSessionController@create
POST /login → AuthenticatedSessionController@store
  - Dicoba dengan guard 'customer' ATAU 'admin' (lihat Discrepancies)
  - Redirect ke dashboard sesuai guard
```

### 3. Melihat Katalog (Tanpa Login)
```
GET  /           → Tampil produk group by nama (maks 3 produk)
                   Filter: jenis_produk != 'topi'
GET  /katalog/{produk} → Detail produk publik
```

### 4. Alur Desain (AKTUAL — WIZARD MULTI-STEP)

```
Customer pilih produk → klik "Mulai Desain"
↓
GET  /customer/design/{produk}?color=... → DesignController@index
  - Load semua template dari DB
  - Cek jika ada ?revisi=id_desain (mode revisi)
  
↓ editor.blade.php dibuka (Alpine.js wizard)

STEP 1: Pilih Warna Baju
  - colorMap: Maroon, Green, Grey, Army, Yellow, White, Navy, Orange, Black, Red, Blue, Mint
  - Simpan ke Alpine.js state: baseColor
  
STEP 2-5: Canvas Editor (Fabric.js)
  - Step 2: Desain Depan (WAJIB ada objek)
  - Step 3: Desain Belakang (opsional, skip)
  - Step 4: Samping Kiri (hanya kaos/hoodie, 6 step)
  - Step 5: Samping Kanan (hanya kaos/hoodie, 6 step)
  [4 step untuk polo/seragam/default: Depan, Belakang, Review]
  
STEP LAST: Review & Simpan
  - Generate preview compositing (mockup + desain)
  - Tampil ringkasan: produk, warna, teknik (selalu "Sablon")
  - Biaya sablon = 0 (pricing dinonaktifkan sementara)
  - Klik "Simpan & Masukkan ke Keranjang"
    ↓
    POST /customer/design (JSON payload)
    → DesignController@store
      - Decode base64 front PNG → simpan storage/app/public/designs/
      - Decode base64 back/left/right jika ada
      - Simpan canvas JSON per sisi (canvas_front, canvas_back, dll.) sebagai longText
      - Simpan raw assets (URL gambar yang diupload customer)
      - Buat record Desain
      - Buat record Cart (langsung, quantity=1, id_desain=desain baru)
      - Return JSON {success, id_desain, redirect_url: /customer/cart}
```

### 5. Revisi Desain
```
Saat order detail berstatus 'revision_required':
Customer klik "Edit Desain"
↓
GET  /customer/design/{produk}?revisi={id_desain_lama}
  - Canvas dimuat dari canvas_front/back/left/right lama
  - Banner merah tampil dengan catatan_admin
  
Klik Simpan →
PATCH /customer/design/{desain}
→ DesignController@update
  - BUAT DESAIN BARU (versioning, bukan update record lama)
  - Set parent_id = id desain lama
  - Update OrderDetail.id_desain → desain baru
  - Update OrderDetail.status_desain → 'pending'
  - Update OrderDetail.catatan_admin → 'Telah diperbaiki...'
  - Return JSON {success, id_desain, redirect_url: /customer/orders}
```

### 6. Keranjang
```
GET  /customer/cart → CartController@index
  - Load cart customer dengan relasi produk & desain
  - Tampil gambar desain, nama produk, harga

PATCH /customer/cart/{cart}/quantity
  - Validasi ownership (id_customer == auth guard id)
  - Update quantity

DELETE /customer/cart/{cart}
  - Validasi ownership
  - Hapus cart item

POST  /customer/cart/direct/{produk}
  - Tambah produk ready-made (tanpa desain, id_desain = null)
  - quantity dari request
```

### 7. Checkout
```
Customer centang item di cart → klik "Checkout"
↓
GET  /customer/checkout?cart_ids[]=1&cart_ids[]=2...
→ CheckoutController@index
  - Validasi cart_ids milik customer
  - Hitung total: (harga_dasar + harga_desain) * quantity per item
  - Tampil halaman konfirmasi

Customer klik "Buat Pesanan"
↓
POST /customer/checkout
→ CheckoutController@store
  - DB::transaction {
      Buat Order: status_order='reviewing', payment_status='unpaid'
      Buat OrderDetail per cart item:
        - id_desain nullable (null = ready-made)
        - status_desain = 'pending' (jika ada desain) atau 'disetujui' (ready-made)
      Hapus cart items yang dicheckout
    }
  - Redirect ke /customer/orders dengan flash success
```

### 8. Upload Bukti Pembayaran
```
Setelah order berstatus 'pending_payment' (payment_status='awaiting_payment' ATAU 'failed'):
↓
GET  /customer/orders/{id} → tampil form upload
POST /customer/orders/{id}/payment
→ CustomerOrderController@uploadPayment
  - Validasi file: image, jpeg/png/jpg, max 2048KB
  - Simpan ke storage/app/public/bukti_pembayaran/
  - Update order: bukti_pembayaran, payment_status='awaiting_verification'
  - Redirect back with success
```

### 9. Tracking Pesanan
```
GET  /customer/orders → list semua order customer (desc tanggal)
GET  /customer/orders/{id} → detail order
  - Status order map: reviewing, pending_payment, processing, completed, cancelled
  - Status tampil dengan label Indonesia dan ikon emoji
```

---

## FLOW ADMIN

### 1. Login Admin
```
POST /login → AuthenticatedSessionController@store (guard 'admin')
  - Cek kredensial di tabel admins
  - Redirect ke /admin/dashboard
```

### 2. Dashboard
```
GET  /admin/dashboard → DashboardController@index
  - Statistik (dikonfirmasi ada controller, detail query belum diaudit)
```

### 3. Kelola Produk
```
CRUD via /admin/products (ResourceController)
  - Validasi: jenis_produk IN (kaos, hoodie, polo)  ← BUKAN seragam/topi
  - Validasi: tipe_produk IN (kustom, jadi)
  - Upload gambar produk ke storage/products/
```

### 4. Kelola Template
```
CRUD via /admin/templates (ResourceController)
  - Upload file template (gambar) ke storage/templates/
```

### 5. Review Desain & Update Status
```
GET  /admin/orders → list semua order
GET  /admin/orders/{order} → detail order + desain + produk

PATCH /admin/orders/{order}/desain/{orderDetail}
→ AdminOrderController@updateStatusDesain
  - Validasi: status_desain IN (approved, revision_required)
  - Update OrderDetail status_desain + catatan_admin
  - AUTO-LOGIC: jika status baru = 'approved' DAN SEMUA detail order approved
    DAN order.status_order = 'reviewing':
      → Update order: status_order='pending_payment', payment_status='awaiting_payment'

PATCH /admin/orders/{order}/status
→ AdminOrderController@updateStatus
  - Validasi: status_order IN (reviewing, pending_payment, processing, completed, cancelled)
  - Manual update status order
```

### 6. Verifikasi Pembayaran
```
PATCH /admin/orders/{order}/verify-payment
→ AdminOrderController@verifyPayment
  - Jika action='approve':
      order.payment_status='paid', order.status_order='processing'
  - Jika action='reject':
      order.payment_status='failed', order.status_order='pending_payment'
```

---

## FLOW OWNER

### 1. Login
Sama dengan admin (guard 'admin', role berbeda).

### 2. Laporan
```
GET  /admin/reports?start_date=&end_date=
→ ReportController@index
  - Default: 30 hari terakhir
  - Metrics: totalOrders, totalRevenue (status=selesai*), totalDesains
  - Growth % vs periode sebelumnya
  - Grafik: status order (doughnut), revenue bulanan (bar)
  - Top 5 produk terlaris
  - Top 5 warna baju populer
  - DataTables: semua transaksi dalam filter

GET  /admin/reports?export_csv=1 → download CSV
```

> ⚠️ **DISCREPANCY:** `totalRevenue` menggunakan `status_order = 'selesai'`, tetapi enum aktual adalah `'completed'`, bukan `'selesai'`. Ini berarti revenue selalu 0 kecuali ada data lama. Lihat `12_DISCREPANCIES.md`.

---

## Status Flow Diagram

### Order Status
```
[customer buat order]
     ↓
  reviewing        ← status_order default saat checkout
     ↓
  (Admin review semua desain)
     ↓ (semua disetujui AUTO)
  pending_payment  ← payment_status='awaiting_payment'
     ↓
  (Customer upload bukti bayar)
     ↓
  payment_status='awaiting_verification'
     ↓
  (Admin verifikasi)
     ↓ approve         ↓ reject
  processing         pending_payment (payment_status='failed')
     ↓
  completed
  
  ATAU bisa dibatalkan kapan saja → cancelled
```

### Design Status (per OrderDetail)
```
  pending          ← default saat checkout (jika ada desain)
  atau
  disetujui        ← untuk produk ready-made (id_desain null)
     ↓
  (Admin review)
     ↓ revision_required    ↓ approved
  [customer revisi]       [lanjut]
       ↓
    pending (desain baru)
```
