# 10 — CONTROLLERS

> **Source of Truth:** Controller files aktual  
> Hanya controller yang DIKONFIRMASI ADA yang didokumentasikan di sini.

---

## Customer\DesignController

**File:** `app/Http/Controllers/Customer/DesignController.php`

### `index(Produk $produk, Request $request)`

**Route:** `GET /customer/design/{produk}`

**Logic:**
1. Cek `?revisi={id_desain}` — mode revisi
2. Jika revisi: ambil `Desain` dengan id tersebut, validasi milik customer
3. Ambil semua template dari DB
4. Tentukan jumlah step wizard: 6 jika `jenis_produk IN [kaos, hoodie]`, 4 sebaliknya
5. Ambil warna awal dari `?color=` atau `$desainRevisi->warna_baju`
6. Pass ke view: `$produk`, `$templates`, `$desainRevisi`, `$totalSteps`, `$initialColor`

### `store(StoreDesainRequest $request)`

**Route:** `POST /customer/design`

**Logic:**
1. Decode `file_desain` (base64 PNG) → simpan ke `storage/app/public/designs/{uniqueName}.png`
2. Jika ada `file_desain_belakang`, `kiri`, `kanan` → decode dan simpan juga
3. Handle `raw_assets`:
   - Untuk setiap URL di array: jika base64 → decode dan simpan ke `designs/assets/`
   - Jika URL eksternal → simpan URL langsung
4. `Desain::create([...])` dengan semua field
5. `Cart::create(['id_customer', 'id_produk', 'id_desain', 'quantity' => 1])` — otomatis ke cart
6. Return JSON: `{'success': true, 'id_desain': ..., 'redirect_url': '/customer/cart'}`

### `update(StoreDesainRequest $request, Desain $desain)`

**Route:** `PATCH /customer/design/{desain}`

**Logic:**
1. Validasi ownership: `$desain->id_customer !== auth guard id` → abort 403
2. Ambil id_produk dari request
3. Proses file desain SAMA seperti store()
4. **BUAT DESAIN BARU** (bukan update desain lama):
   ```php
   $newDesain = Desain::create([...all fields..., 'parent_id' => $desain->id_desain])
   ```
5. Cari `OrderDetail` yang menggunakan `id_desain = $desain->id_desain`
6. Update OrderDetail: `id_desain → $newDesain->id_desain`, `status_desain → 'pending'`, `catatan_admin → 'Telah diperbaiki...'`
7. Return JSON: `{'success': true, 'id_desain': ..., 'redirect_url': '/customer/orders'}`

---

## Customer\CartController

**File:** `app/Http/Controllers/Customer/CartController.php`

### `index()`
Load cart customer dengan relasi `produk` dan `desain`.

### `storeDirect(Request $request, Produk $produk)`
Tambah produk ready-made ke cart tanpa desain (`id_desain = null`).  
Validasi: `quantity` required, integer, min:1.

### `updateQuantity(Request $request, Cart $cart)`
Update quantity cart item.  
Validasi: customer ownership + quantity valid.

### `destroy(Cart $cart)`
Hapus cart item setelah validasi ownership.

---

## Customer\CheckoutController

**File:** `app/Http/Controllers/Customer/CheckoutController.php`

### `index(Request $request)`

**Route:** `GET /customer/checkout`

**Logic:**
1. Validasi: `cart_ids` required, array, each exists in carts
2. Ambil cart items berdasarkan `cart_ids`, filter hanya milik customer login
3. Kalkulasi total: `(harga_dasar + harga_desain) * quantity` per item
4. Pass ke view

### `store(StoreCheckoutRequest $request)`

**Route:** `POST /customer/checkout`

**Logic (ATOMIC TRANSACTION):**
```php
DB::transaction(function() {
    // 1. Hitung total
    // 2. Buat Order
    $order = Order::create([
        'id_customer'    => customerId,
        'tanggal_order'  => now(),
        'status_order'   => 'reviewing',
        'payment_status' => 'unpaid',
        'total_harga'    => $total,
    ]);
    
    // 3. Buat OrderDetail per cart item
    foreach($cartItems as $cart) {
        $hasDesain = $cart->id_desain !== null;
        OrderDetail::create([
            'id_order'       => $order->id_order,
            'id_produk'      => $cart->id_produk,
            'id_desain'      => $cart->id_desain,
            'quantity'       => $cart->quantity,
            'harga_produk'   => $cart->produk->harga_dasar,
            'harga_desain'   => $hasDesain ? $cart->desain->harga_desain : 0,
            'subtotal'       => calculated,
            'status_desain'  => $hasDesain ? 'pending' : 'disetujui',  // BUG: 'disetujui' ≠ 'approved'
        ]);
    }
    
    // 4. Hapus cart items yang dicheckout
    Cart::whereIn('id_cart', $cart_ids)->delete();
});

return redirect()->route('customer.orders.index')->with('success', '...');
```

---

## Customer\OrderController

**File:** `app/Http/Controllers/Customer/OrderController.php`

### `index()`
List semua order customer, ordered by `tanggal_order` desc.

### `show($id)`
Detail order dengan relasi `orderDetails.produk` dan `orderDetails.desain`.  
Filter ownership: `where('id_customer', auth guard id)`.

### `uploadPayment(Request $request, $id)`
Upload bukti pembayaran.  
Syarat: `payment_status IN ['awaiting_payment', 'failed']`.  
Update: `bukti_pembayaran`, `payment_status = 'awaiting_verification'`.

---

## Admin\OrderController

**File:** `app/Http/Controllers/Admin/OrderController.php`

### `index()`
Load semua orders dengan relasi `customer` dan `orderDetails`.  
Tidak ada paginasi — memuat semua sekaligus.

### `show(Order $order)`
Detail order dengan relasi lengkap: `orderDetails.produk`, `orderDetails.desain`.

### `updateStatus(Request $request, Order $order)`
Update `status_order` manual oleh admin.  
Validasi: `status_order IN ['reviewing', 'pending_payment', 'processing', 'completed', 'cancelled']`.

### `updateStatusDesain(Request $request, Order $order, OrderDetail $orderDetail)`
Review desain per order detail.  
Validasi: `status_desain IN ['approved', 'revision_required']`.

**AUTO-TRANSITION LOGIC:**
```php
if ($newStatus === 'approved') {
    $allApproved = $order->orderDetails
        ->where('id_order_detail', '!=', $orderDetail->id)
        ->every(fn($d) => $d->status_desain === 'approved');
    
    if ($allApproved && $order->status_order === 'reviewing') {
        $order->update([
            'status_order'   => 'pending_payment',
            'payment_status' => 'awaiting_payment',
        ]);
    }
}
```

Juga update: `catatan_admin` jika ada.

### `verifyPayment(Request $request, Order $order)`
Verifikasi bukti pembayaran.  
- `action = 'approve'` → `payment_status = 'paid'`, `status_order = 'processing'`
- `action = 'reject'` → `payment_status = 'failed'`, `status_order = 'pending_payment'`

---

## Admin\ProductController

**File:** `app/Http/Controllers/Admin/ProductController.php`

**Standard CRUD dengan validasi:**
- `jenis_produk IN ['kaos', 'hoodie', 'polo']`
- `tipe_produk IN ['kustom', 'jadi']`
- Gambar: `image|mimes:jpeg,png,jpg|max:2048`
- Storage disk: `public`

---

## Admin\TemplateController

**File:** `app/Http/Controllers/Admin/TemplateController.php`

**Standard CRUD untuk template desain.**  
Upload file ke `storage/app/public/templates/`.

---

## Admin\DashboardController

**File:** `app/Http/Controllers/Admin/DashboardController.php`

Detail implementasi belum diaudit mendalam.  
Mengambil statistik dasar untuk dashboard admin.

---

## Owner\ReportController

**File:** `app/Http/Controllers/Owner/ReportController.php`

### `index(Request $request)`

**Metriks yang dihitung:**
- `totalOrders`: count orders dalam period
- `totalRevenue`: sum total_harga WHERE `status_order = 'selesai'` ← **BUG: harus 'completed'**
- `totalDesains`: count desains dalam period
- `ordersGrowth`: % dibanding period sebelumnya
- `revenueGrowth`: % dibanding period sebelumnya
- Status distribution: count per status_order (untuk grafik donut)
- Monthly revenue: sum per bulan (untuk grafik bar)
- Top 5 produk terlaris (by quantity dari order_details)
- Top 5 warna baju populer (dari desains.warna_baju)
- Semua transaksi (untuk DataTables)

### `exportCsv(Request $request)`

Format CSV:
```
ID Invoice,Tanggal,Nama Customer,Item Dibeli,Total Harga (Rp),Status Pembayaran
INV-2026XXXXX,...
```

---

## FormRequests

### StoreDesainRequest

**File:** `app/Http/Requests/Customer/StoreDesainRequest.php`

```php
public function authorize(): bool {
    return auth()->guard('customer')->check();
}

public function rules(): array {
    return [
        'id_produk'             => 'required|exists:produks,id_produk',
        'file_desain'           => 'required|string',      // base64
        'file_desain_belakang'  => 'nullable|string',
        'file_desain_kiri'      => 'nullable|string',
        'file_desain_kanan'     => 'nullable|string',
        'canvas_front'          => 'nullable|string',      // JSON
        'canvas_back'           => 'nullable|string',
        'canvas_left'           => 'nullable|string',
        'canvas_right'          => 'nullable|string',
        'warna_baju'            => 'nullable|string|max:20',
        'raw_assets'            => 'nullable|array',
        'harga_desain'          => 'required|numeric|min:0',
        'detail_sablon'         => 'nullable|string',
    ];
}
```

### StoreCheckoutRequest

**File:** `app/Http/Requests/Customer/StoreCheckoutRequest.php`

```php
public function authorize(): bool {
    return auth()->guard('customer')->check();
}

public function rules(): array {
    return [
        'cart_ids'   => 'required|array',
        'cart_ids.*' => 'exists:carts,id_cart',
    ];
}
```

---

## Middleware

### CheckRole

**File:** `app/Http/Middleware/CheckRole.php`

```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    $user = auth()->guard('admin')->user();
    
    if (!$user || !in_array($user->role, $roles)) {
        abort(403, 'Unauthorized');
    }
    
    return $next($request);
}
```

**Penggunaan di route:**
```php
->middleware(['auth:admin', 'role:admin'])
->middleware(['auth:admin', 'role:owner,admin'])
```
