# Bab 4: Pengujian Sistem (White Box Testing)

Pengujian *White Box* dilakukan untuk mengevaluasi logika internal dari kode program sistem. Fokus utama dari pengujian ini adalah memastikan seluruh alur percabangan (*branching*), perulangan (*looping*), dan kondisi penentu dalam kode dieksekusi dengan benar tanpa menghasilkan galat (*error*).

Untuk mempermudah pengambilan dokumentasi visual (screenshot flowchart dan tabel flowgraph) untuk dokumen skripsi Anda, silakan buka file berikut di *browser* Anda:
👉 **[Buka Visualisasi Diagram (HTML)](file:///c:/laragon/www/percobaanskripsi_1/docs/view_diagrams.html)**

---

## 4.2.2.1 Pengujian White Box Halaman Login
Proses login diverifikasi melalui alur request authentikasi yang membatasi upaya login (*rate limiting*) serta mendistribusikan pengguna sesuai dengan *Guard* / hak aksesnya masing-masing (Customer vs Admin/Owner).

### A. Penentuan Baris Kode (Source Code) yang Diuji
Kode logika disederhanakan dari `AuthenticatedSessionController.php` dan `LoginRequest.php` untuk mempermudah visualisasi simpul:

```php
1.  public function login(LoginRequest $request) {
2.      $this->ensureIsNotRateLimited();
3.      if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
4.          RateLimiter::clear($request->throttleKey());
5.          return redirect()->intended(route('customer.dashboard'));
6.      }
7.      if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
8.          RateLimiter::clear($request->throttleKey());
9.          return redirect()->intended(route('admin.dashboard'));
10.     }
11.     RateLimiter::hit($request->throttleKey());
12.     throw ValidationException::withMessages(['email' => 'Failed']);
13. }
```

### B. Pemetaan Simpul (Node Mapping)
*   **Node 1:** Baris 2 (Cek status pembatasan percobaan login / *rate limit*).
*   **Node 2:** Baris 3 (Kondisi: *Apakah kredensial cocok dengan guard 'customer'?*).
*   **Node 3:** Baris 4-5 (Jika *True*: Bersihkan limit percobaan, arahkan ke dasbor pelanggan).
*   **Node 4:** Baris 7 (Jika *False*: Pengecekan guard 'admin'/'owner').
*   **Node 5:** Baris 8-9 (Jika *True*: Bersihkan limit percobaan, arahkan ke dasbor admin/owner).
*   **Node 6:** Baris 11-12 (Jika *False*: Catat percobaan gagal, lemparkan pesan validasi eror).
*   **Node 7:** Selesai (End).

---

## 4.2.2.2 Pengujian White Box Halaman Editor Desain Customer (Backend)
Proses simpan desain dijalankan saat pelanggan mengklik tombol "Simpan & Lanjutkan" di halaman editor. Alur *backend*-nya diuji pada fungsi `store()` di dalam `DesignController.php` untuk memastikan proses dekode gambar *canvas* berjalan aman.

### A. Penentuan Baris Kode (Source Code) yang Diuji
Kode program disederhanakan dan diberi nomor baris:

```php
1.  public function store(StoreDesainRequest $request) {
2.      $base64_image = $request->file_desain;
3.      if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
4.          $image_base64 = substr($base64_image, strpos($base64_image, ',') + 1);
5.          $image_data = base64_decode($image_base64);
6.          $fileName = 'designs/' . Str::random(40) . '.png';
7.          Storage::disk('public')->put($fileName, $image_data);
8.      }
9.      $fileNameBelakang = null;
10.     if ($request->filled('file_desain_belakang')) {
11.         $base64_image_belakang = $request->file_desain_belakang;
12.         if (preg_match('/^data:image\/(\w+);base64,/', $base64_image_belakang, $typeBelakang)) {
13.             $image_base64_b = substr($base64_image_belakang, strpos($base64_image_belakang, ',') + 1);
14.             $image_data_b = base64_decode($image_base64_b);
15.             $fileNameBelakang = 'designs/' . Str::random(40) . '-back.png';
16.             Storage::disk('public')->put($fileNameBelakang, $image_data_b);
17.         }
18.     }
19.     $desain = Desain::create([...]);
20.     Cart::create([...]);
21.     return response()->json(['success' => true]);
22. }
```

### B. Pemetaan Simpul (Node Mapping)
*   **Node 1:** Baris 2 (Mengambil parameter `file_desain` depan dan mencocokkan regex MIME Base64).
*   **Node 2:** Baris 4-7 (Jika cocok: Proses *decode* string Base64 dan simpan file ke direktori disk publik).
*   **Node 3:** Baris 9-10 (Jika tidak cocok / Setelah simpan depan: Inisialisasi variabel gambar belakang dan cek apakah data desain belakang diisi).
*   **Node 4:** Baris 11-12 (Jika diisi: Cocokkan regex MIME Base64 gambar belakang).
*   **Node 5:** Baris 13-16 (Jika cocok: Lakukan proses *decode* string Base64 gambar belakang dan simpan berkas).
*   **Node 6:** Baris 19-21 (Jika tidak cocok / Setelah proses selesai: Simpan data desain baru ke database, buat relasi keranjang belanja, dan siapkan response sukses).
*   **Node 7:** Selesai (End).

---

## 4.2.2.3 Pengujian White Box Halaman Checkout Customer
Proses *Checkout* (penyimpanan pesanan) diuji pada fungsi `store()` di dalam `CheckoutController.php`. Fungsi ini bertanggung jawab untuk memvalidasi keranjang belanja, menghitung subtotal harga secara dinamis (harga produk dasar + biaya sablon custom), mengunggah bukti pembayaran, serta menyimpan transaksi ke dalam database.

### A. Penentuan Baris Kode (Source Code) yang Diuji
Kode program disederhanakan dan diberi penomoran baris untuk mempermudah pemetaan simpul (*node*):

```php
1.  public function store(StoreCheckoutRequest $request) {
2.      $id_customer = Auth::guard('customer')->id();
3.      $carts = Cart::where('id_customer', $id_customer)->get();
4.      if ($carts->isEmpty()) {
5.          return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
6.      }
7.      $totalHarga = 0;
8.      foreach ($carts as $cart) {
9.          if ($cart->desain) {
10.             $hargaDesain = $cart->desain->harga_desain;
11.         } else {
12.             $hargaDesain = 0;
13.         }
14.         $totalHarga += ($cart->produk->harga_dasar + $hargaDesain) * $cart->quantity;
15.     }
16.     $buktiPath = null;
17.     if ($request->hasFile('bukti_pembayaran')) {
18.         // ... proses upload file bukti transfer ...
19.         $buktiPath = 'bukti_pembayaran/' . $filename;
20.     }
21.     $order = DB::transaction(function () use (...) {
22.         $order = Order::create([...]);
23.         foreach ($carts as $cart) {
24.             OrderDetail::create([...]);
25.         }
26.         Cart::where('id_customer', $id_customer)->delete();
27.         return $order;
28.     });
29.     return redirect()->route('orders.index')->with('success', 'Sukses');
30. }
```

### B. Pemetaan Simpul (Node Mapping)
*   **Node 1:** Baris 2-3 (Inisialisasi `id_customer` dan mengambil data `carts`).
*   **Node 2:** Baris 4 (Pengecekan kondisi: *Apakah keranjang kosong?*).
*   **Node 3:** Baris 5 (Jika *True*: Kembalikan respon redirect error).
*   **Node 4:** Baris 7 (Jika *False*: Inisialisasi variabel `totalHarga` dengan nilai 0).
*   **Node 5:** Baris 8 (Pengecekan perulangan pertama: `foreach ($carts as $cart)`).
*   **Node 6:** Baris 9 (Pengecekan kondisi: *Apakah produk memiliki desain custom?*).
*   **Node 7:** Baris 10 (Jika *True*: Set `hargaDesain` sesuai harga dari database).
*   **Node 8:** Baris 12 (Jika *False*: Set `hargaDesain` = 0).
*   **Node 9:** Baris 14 (Hitung subtotal per item, akumulasikan ke `totalHarga`, lalu kembali ke Node 5).
*   **Node 10:** Baris 16-17 (Pengecekan kondisi: *Apakah terdapat input file bukti pembayaran?*).
*   **Node 11:** Baris 18-19 (Jika *True*: Lakukan proses upload berkas bukti pembayaran).
*   **Node 12:** Baris 21-22 (Jika *False* / Setelah Upload: Mulai DB Transaction & simpan data `orders`).
*   **Node 13:** Baris 23 (Pengecekan perulangan kedua: `foreach ($carts as $cart)` untuk detail order).
*   **Node 14:** Baris 24 (Simpan rincian data ke `order_details` lalu kembali ke Node 13).
*   **Node 15:** Baris 26-28 (Hapus isi keranjang belanja).
*   **Node 16:** Baris 29 (Kembalikan respon redirect sukses).
*   **Node 17:** Selesai (End).

---

## 4.2.2.4 Pengujian White Box Halaman Mengelola Pesanan (Admin - Update Status Desain)
Proses pengelolaan status pesanan oleh admin diuji untuk mengkonfirmasi status persetujuan desain sablon dari pelanggan. Alur logika berada di `OrderController.php` pada fungsi `updateStatusDesain()`.

### A. Penentuan Baris Kode (Source Code) yang Diuji
Kode program disederhanakan dan diberi nomor baris:

```php
1.  public function updateStatusDesain(Request $request, Order $order, OrderDetail $orderDetail) {
2.      $request->validate([
3.          'status_desain' => 'required|in:disetujui,revisi',
4.          'catatan_admin' => 'nullable|string'
5.      ]);
6.      if ($request->status_desain === 'disetujui') {
7.          $orderDetail->update([
8.              'status_desain' => 'disetujui',
9.              'catatan_admin' => null
10.         ]);
11.     } else {
12.         $orderDetail->update([
13.             'status_desain' => 'revisi',
14.             'catatan_admin' => $request->catatan_admin
15.         ]);
16.     }
17.     return back()->with('success', 'Status & catatan desain diperbarui.');
18. }
```

### B. Pemetaan Simpul (Node Mapping)
*   **Node 1:** Baris 2-5 (Melakukan pengecekan validasi data formulir masukan).
*   **Node 2:** Baris 6 (Pengecekan kondisi: *Apakah status desain disetujui?*).
*   **Node 3:** Baris 7-10 (Jika *True*: Update status desain disetujui, hapus catatan revisi lama).
*   **Node 4:** Baris 12-15 (Jika *False*: Update status desain menjadi revisi, masukkan catatan admin).
*   **Node 5:** Baris 17 (Kembalikan respon kembali ke halaman detail order dengan status sukses).
*   **Node 6:** Selesai (End).
