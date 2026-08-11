# 09 — MODELS & RELATIONSHIPS

> **Source of Truth:** Model files aktual di `app/Models/`

---

## App\Models\Customer

**File:** `app/Models/Customer.php`  
**Table:** `customers`  
**Primary Key:** `id_customer`  
**Guard:** `customer`

```php
class Customer extends Authenticatable
{
    use HasFactory;
    
    protected $primaryKey = 'id_customer';
    
    protected $fillable = [
        'nama_customer', 'email', 'password', 
        'no_hp', 'alamat'
    ];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected function casts(): array {
        return ['password' => 'hashed'];
    }
}
```

**Relationships (dari migration/controller, bukan dari model file):**
- `hasMany(Order::class, 'id_customer')`
- `hasMany(Desain::class, 'id_customer')`
- `hasMany(Cart::class, 'id_customer')`

> ⚠️ **CATATAN:** Relationship methods mungkin tidak dideklarasikan di model file. Perlu verifikasi langsung.

---

## App\Models\Admin

**File:** `app/Models/Admin.php`  
**Table:** `admins`  
**Primary Key:** `id_admin`  
**Guard:** `admin`

```php
class Admin extends Authenticatable
{
    use HasFactory;
    
    protected $primaryKey = 'id_admin';
    
    protected $fillable = [
        'nama_admin', 'email', 'password', 'role'
    ];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected function casts(): array {
        return ['password' => 'hashed'];
    }
}
```

**Role values:** `admin`, `owner`

---

## App\Models\Produk

**File:** `app/Models/Produk.php`  
**Table:** `produks`  
**Primary Key:** `id_produk`

```php
protected $primaryKey = 'id_produk';

protected $fillable = [
    'nama_produk', 'jenis_produk', 'tipe_produk',
    'harga_dasar', 'deskripsi', 'gambar_produk'
];
```

**Validasi jenis_produk (aktual):** `kaos`, `hoodie`, `polo`  
**Validasi tipe_produk (aktual):** `kustom`, `jadi`

---

## App\Models\Template

**File:** `app/Models/Template.php`  
**Table:** `templates`  
**Primary Key:** `id_template`

```php
protected $primaryKey = 'id_template';

protected $fillable = [
    'id_admin', 'nama_template', 'file_template'
];
```

**Relationships:**
- `belongsTo(Admin::class, 'id_admin')`

---

## App\Models\Desain

**File:** `app/Models/Desain.php`  
**Table:** `desains`  
**Primary Key:** `id_desain`

```php
protected $primaryKey = 'id_desain';

protected $fillable = [
    'parent_id', 'id_customer', 'id_template',
    'file_desain', 'file_desain_belakang', 
    'file_desain_kiri', 'file_desain_kanan',
    'warna_baju',
    'canvas_front', 'canvas_back', 'canvas_left', 'canvas_right',
    'harga_desain', 'raw_assets', 'detail_sablon'
];

protected $casts = [
    'raw_assets' => 'array',
];
```

**Relationships:**
- `belongsTo(Customer::class, 'id_customer')`
- `belongsTo(Template::class, 'id_template')`
- Self-referential (versioning): parent `id_desain` ← child `parent_id`

---

## App\Models\Cart

**File:** `app/Models/Cart.php`  
**Table:** `carts`  
**Primary Key:** `id_cart`

```php
protected $primaryKey = 'id_cart';

protected $fillable = [
    'id_customer', 'id_produk', 'id_desain', 'quantity'
];
```

**Relationships:**
- `belongsTo(Customer::class, 'id_customer')`
- `belongsTo(Produk::class, 'id_produk')`
- `belongsTo(Desain::class, 'id_desain')` (nullable)

---

## App\Models\Order

**File:** `app/Models/Order.php`  
**Table:** `orders`  
**Primary Key:** `id_order`

```php
protected $primaryKey = 'id_order';

protected $fillable = [
    'id_customer', 'tanggal_order', 'status_order', 
    'total_harga', 'bukti_pembayaran'
    // CATATAN: 'payment_status' mungkin TIDAK ada di fillable
];
```

**Enum status_order (AKTUAL dari migration):**
```
'reviewing', 'pending_payment', 'processing', 'completed', 'cancelled'
```

**Enum payment_status (AKTUAL dari migration):**
```
'unpaid', 'awaiting_payment', 'awaiting_verification', 'paid', 'failed'
```

**Relationships:**
- `belongsTo(Customer::class, 'id_customer')`
- `hasMany(OrderDetail::class, 'id_order')`

> ⚠️ **PENTING:** `payment_status` ditambahkan migration 2026-08-07. Jika tidak ada di `$fillable`, maka `Order::create(['payment_status' => ...])` TIDAK akan mengisi field ini (mass assignment protection). `Order::where(...)->update(['payment_status' => ...])` masih bekerja.

---

## App\Models\OrderDetail

**File:** `app/Models/OrderDetail.php`  
**Table:** `order_details`  
**Primary Key:** `id_order_detail`

```php
protected $primaryKey = 'id_order_detail';

protected $fillable = [
    'id_order', 'id_produk', 'id_desain', 'quantity',
    'harga_produk', 'harga_desain', 'subtotal',
    'status_desain', 'catatan_admin'
];
```

**Enum status_desain (AKTUAL dari migration):**
```
'pending', 'revision_required', 'approved'
```

**Catatan:** Nilai `'disetujui'` (string bahasa Indonesia) digunakan untuk ready-made products di CheckoutController.  
⚠️ **BUG:** `'disetujui'` BUKAN nilai yang valid dalam enum database aktual (`'approved'`).  
Lihat `12_DISCREPANCIES.md` → Discrepancy #4.

**Relationships:**
- `belongsTo(Order::class, 'id_order')`
- `belongsTo(Produk::class, 'id_produk')`
- `belongsTo(Desain::class, 'id_desain')` (nullable)

---

## App\Models\User

**File:** `app/Models/User.php`  
**Table:** `users`  
**Status:** ⚠️ Tabel `users` telah DIHAPUS dalam migration cleanup 2026-08-01

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    // ...
}
```

Model ini masih ada di codebase (dibutuhkan oleh Laravel Fortify dan paket lain) tapi TIDAK digunakan dalam alur bisnis aktual. Tabel di database sudah tidak ada.

---

## Ringkasan Relationships

```
customers
  ├── id_customer ─── carts.id_customer (hasMany)
  ├── id_customer ─── orders.id_customer (hasMany)
  └── id_customer ─── desains.id_customer (hasMany)

admins
  └── id_admin ─── templates.id_admin (hasMany)

produks
  ├── id_produk ─── carts.id_produk (hasMany)
  └── id_produk ─── order_details.id_produk (hasMany)

templates
  └── id_template ─── desains.id_template (hasMany, nullable)

desains
  ├── id_desain ─── carts.id_desain (hasMany, nullable)
  ├── id_desain ─── order_details.id_desain (hasMany, nullable)
  └── id_desain ─── desains.parent_id (self-ref hasMany children)

orders
  └── id_order ─── order_details.id_order (hasMany)

order_details
  ├── belongsTo orders
  ├── belongsTo produks
  └── belongsTo desains (nullable)
```

---

## Auth Providers (config/auth.php)

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',    // Model User
    ],
    'customer' => [
        'driver' => 'session',
        'provider' => 'customers',   // Model Customer
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',      // Model Admin
    ],
],

'providers' => [
    'users'     => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    'customers' => ['driver' => 'eloquent', 'model' => App\Models\Customer::class],
    'admins'    => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
],
```
