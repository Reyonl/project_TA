# 02 — ARCHITECTURE

> **Source of Truth:** Source code aktual  
> Ini adalah arsitektur yang BENAR-BENAR ADA, bukan arsitektur ideal.

---

## Overview Pattern

Aplikasi menggunakan arsitektur **Laravel MVC** dengan modifikasi berikut:
- Controller-centric (logika bisnis di controller, bukan service layer)
- Livewire diinstall tapi **belum digunakan secara aktif** dalam fitur bisnis utama (hanya paket Flux/Blaze untuk starter kit)
- FormRequest digunakan untuk validasi design (StoreDesainRequest, StoreCheckoutRequest)
- Blade views dengan Alpine.js untuk interaktivitas UI
- JavaScript inline heavy (canvas editor ~1534 baris dalam satu file Blade)

---

## Directory Structure (Relevan)

```
app/
├── Actions/          → (kosong, belum digunakan)
├── Concerns/         → (kosong, belum digunakan)
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php         (base)
│   │   ├── ProfileController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── OrderController.php
│   │   │   ├── ProductController.php
│   │   │   └── TemplateController.php
│   │   ├── Auth/
│   │   │   └── (AuthenticatedSessionController, RegisteredUserController, dll.)
│   │   ├── Customer/
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── DesignController.php
│   │   │   ├── OrderController.php
│   │   │   └── ProductController.php
│   │   └── Owner/
│   │       └── ReportController.php
│   ├── Middleware/
│   │   └── CheckRole.php           (cek role admin/owner)
│   └── Requests/
│       └── Customer/
│           ├── StoreDesainRequest.php
│           └── StoreCheckoutRequest.php
├── Livewire/
│   └── Actions/      → (belum digunakan untuk fitur bisnis)
├── Models/
│   ├── Admin.php
│   ├── Cart.php
│   ├── Customer.php
│   ├── Desain.php
│   ├── Order.php
│   ├── OrderDetail.php
│   ├── Produk.php
│   ├── Template.php
│   └── User.php      (tidak digunakan dalam bisnis utama)
├── Providers/         → AppServiceProvider
└── View/              → (belum digunakan)
```

---

## Models & Relationships

```
Customer (id_customer)
    ├── hasMany → Desain (id_customer)
    ├── hasMany → Order (id_customer)
    └── hasMany → Cart (id_customer)

Admin (id_admin)
    └── hasMany → Template (id_admin)

Template (id_template)
    ├── belongsTo → Admin
    └── hasMany → Desain (id_template, nullable)

Desain (id_desain)
    ├── belongsTo → Customer
    ├── belongsTo → Template (nullable)
    ├── self-referential → parent_id (nullable, untuk versioning revisi)
    └── hasMany → OrderDetail

Order (id_order)
    ├── belongsTo → Customer
    └── hasMany → OrderDetail

OrderDetail (id_order_detail)
    ├── belongsTo → Order
    ├── belongsTo → Produk
    └── belongsTo → Desain (nullable, null = produk ready-made)

Produk (id_produk)
    └── hasMany → OrderDetail

Cart (id_cart)
    ├── belongsTo → Customer
    ├── belongsTo → Produk
    └── belongsTo → Desain (nullable)
```

---

## Controllers (Aktual)

### Customer Controllers

| Controller | Fungsi | Route Prefix |
|---|---|---|
| `Customer\ProductController` | index (dashboard), show (katalog) | `/customer/`, `/katalog/` |
| `Customer\DesignController` | index (buka editor), store (simpan baru), update (revisi) | `/customer/design/` |
| `Customer\CartController` | index, storeDirect, updateQuantity, destroy | `/customer/cart/` |
| `Customer\CheckoutController` | index (preview), store (buat order) | `/customer/checkout/` |
| `Customer\OrderController` | index (list), show (detail), uploadPayment | `/customer/orders/` |

### Admin Controllers

| Controller | Fungsi | Route Prefix |
|---|---|---|
| `Admin\DashboardController` | index | `/admin/dashboard` |
| `Admin\ProductController` | CRUD produk (kaos, hoodie, polo) | `/admin/products/` |
| `Admin\TemplateController` | CRUD template desain | `/admin/templates/` |
| `Admin\OrderController` | index, show, updateStatus, updateStatusDesain, verifyPayment | `/admin/orders/` |

### Owner Controllers

| Controller | Fungsi | Route Prefix |
|---|---|---|
| `Owner\ReportController` | index, exportCsv | `/admin/reports/` |

---

## Middleware Stack

| Middleware | Kegunaan |
|---|---|
| `auth:customer` | Proteksi route customer |
| `auth:admin` | Proteksi route admin dan owner |
| `role:admin` | Sub-proteksi: hanya role admin (via CheckRole middleware) |
| `role:owner,admin` | Sub-proteksi: role owner ATAU admin |

`CheckRole` middleware membaca `$user->role` dari tabel `admins`.

---

## Route Architecture

```
GET  /                    → Welcome page (produk publik, terbatas 3)
GET  /katalog/{produk}   → Detail produk publik (tanpa login)

/customer/*              → auth:customer
  /dashboard             → ProductController@index
  /products/{produk}     → ProductController@show
  /design/{produk}       → DesignController@index (editor)
  POST /design           → DesignController@store
  PATCH /design/{desain} → DesignController@update (revisi)
  /checkout              → CheckoutController
  /cart                  → CartController
  /orders                → OrderController
  GET /api/stickers      → Iconify + DiceBear proxy

/admin/*                 → auth:admin
  /dashboard             → DashboardController@index
  [role:admin]
    /products            → ProductController (CRUD)
    /templates           → TemplateController (CRUD)
    /orders              → OrderController (index, show, updateStatus, updateStatusDesain, verifyPayment)
  [role:owner,admin]
    /reports             → ReportController@index

GET /storage/{path}      → PHP file serving fallback (untuk shared hosting)
GET /debug-storage       → Debug route (masih aktif, perlu dihapus)

/auth.php routes         → Register, Login, Logout, Password Reset
/settings.php routes     → Profile settings
```

---

## Blade View Architecture

```
resources/views/
├── welcome.blade.php              → Landing page publik
├── dashboard.blade.php            → (legacy/unused?)
├── layouts/
│   ├── app.blade.php              → Layout utama dengan header
│   └── ...
├── components/                    → Blade components
├── customer/
│   ├── products/
│   │   └── show.blade.php         → Detail produk
│   ├── designs/
│   │   ├── editor.blade.php       → Wrapper step-by-step wizard
│   │   ├── _canvas_editor.blade.php → HTML canvas editor (partial)
│   │   ├── _editor_scripts.blade.php → JS Fabric.js (1534 baris)
│   │   ├── _editor_styles.blade.php  → CSS editor khusus
│   │   └── editor_old.blade.php   → Legacy (tidak digunakan)
│   ├── cart/
│   │   └── index.blade.php
│   ├── checkout/
│   │   └── index.blade.php
│   └── orders/
│       ├── index.blade.php
│       └── show.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── orders/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── templates/
│       ├── index.blade.php
│       └── create.blade.php
└── owner/
    └── reports/
        └── index.blade.php
```

---

## FormRequests (Validasi)

| FormRequest | Digunakan Oleh |
|---|---|
| `StoreDesainRequest` | `DesignController@store`, `DesignController@update` |
| `StoreCheckoutRequest` | `CheckoutController@store` |

> **CATATAN:** Tidak semua controller menggunakan FormRequest. `CartController`, `OrderController` (customer), `Admin\OrderController` masih menggunakan `$request->validate()` inline di controller.

---

## Pola yang TIDAK Digunakan (CONFIRMED)

- **Service Layer** — tidak ada `app/Services/`
- **Repository Pattern** — tidak ada `app/Repositories/`
- **Policy / Gate** — tidak ada file di `app/Policies/`, authorization dilakukan inline di controller
- **Observer** — tidak ada
- **Event/Listener** — tidak ada di fitur bisnis
- **Queue/Job** — tabel jobs dihapus dalam migration cleanup (2026-08-01)
- **Scheduled Tasks** — `console.php` kosong
- **Livewire Components (aktif)** — hanya ada `app/Livewire/Actions/` yang kosong
