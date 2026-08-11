# 07 — FILE MAP

> **Source of Truth:** Source code aktual. Semua path telah dikonfirmasi ada.  
> Tidak ada path fiktif di dokumen ini.

---

## Fitur → File Map

### Authentication (Customer)

| Komponen | File |
|---|---|
| Route | `routes/auth.php` |
| Controller | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| Controller | `app/Http/Controllers/Auth/RegisteredUserController.php` |
| Controller | `app/Http/Controllers/Auth/PasswordResetLinkController.php` |
| Controller | `app/Http/Controllers/Auth/NewPasswordController.php` |
| Model | `app/Models/Customer.php` |
| View login | `resources/views/auth/login.blade.php` |
| View register | `resources/views/auth/register.blade.php` |

### Authentication (Admin/Owner)

| Komponen | File |
|---|---|
| Route | `routes/auth.php` (same) |
| Middleware CheckRole | `app/Http/Middleware/CheckRole.php` |
| Model | `app/Models/Admin.php` |

---

### Landing Page / Katalog Publik

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET /, GET /katalog/{produk}) |
| View landing | `resources/views/welcome.blade.php` |
| Controller | `app/Http/Controllers/Customer/ProductController.php` (katalog.show) |
| Model | `app/Models/Produk.php` |

---

### Canvas Design Editor

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET /customer/design/{produk}, POST /customer/design, PATCH /customer/design/{desain}) |
| Controller | `app/Http/Controllers/Customer/DesignController.php` |
| FormRequest | `app/Http/Requests/Customer/StoreDesainRequest.php` |
| Model Desain | `app/Models/Desain.php` |
| Model Template | `app/Models/Template.php` |
| Model Produk | `app/Models/Produk.php` |
| View wrapper | `resources/views/customer/designs/editor.blade.php` |
| View HTML editor | `resources/views/customer/designs/_canvas_editor.blade.php` |
| View JS editor | `resources/views/customer/designs/_editor_scripts.blade.php` |
| View CSS editor | `resources/views/customer/designs/_editor_styles.blade.php` |
| Fabric.js library | `public/js/fabric.min.js` |
| Smart guidelines | `public/js/fabric-smart-guides.js` |
| Mockup images | `public/images/mockups/{mockupBase}.png` (dan variant _belakang, _samping_kiri, _samping_kanan) |
| Template assets | Storage: `storage/app/public/templates/` |
| Sticker API route | `routes/web.php` (GET /customer/api/stickers) |
| Storage desain | `storage/app/public/designs/` dan `storage/app/public/designs/assets/` |

---

### Keranjang Belanja (Cart)

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET/POST/PATCH/DELETE /customer/cart*) |
| Controller | `app/Http/Controllers/Customer/CartController.php` |
| Model | `app/Models/Cart.php` |
| View | `resources/views/customer/cart/index.blade.php` |

---

### Checkout & Pembayaran

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET/POST /customer/checkout) |
| Controller | `app/Http/Controllers/Customer/CheckoutController.php` |
| FormRequest | `app/Http/Requests/Customer/StoreCheckoutRequest.php` |
| Model Order | `app/Models/Order.php` |
| Model OrderDetail | `app/Models/OrderDetail.php` |
| Model Cart | `app/Models/Cart.php` |
| View | `resources/views/customer/checkout/index.blade.php` |

---

### Riwayat Pesanan Customer

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET /customer/orders, GET /customer/orders/{id}, POST /customer/orders/{id}/payment) |
| Controller | `app/Http/Controllers/Customer/OrderController.php` |
| Model | `app/Models/Order.php` |
| Model | `app/Models/OrderDetail.php` |
| View list | `resources/views/customer/orders/index.blade.php` |
| View detail | `resources/views/customer/orders/show.blade.php` |
| Upload bukti | `storage/app/public/bukti_pembayaran/` |

---

### Admin: Kelola Produk

| Komponen | File |
|---|---|
| Route | `routes/web.php` (resource /admin/products) |
| Controller | `app/Http/Controllers/Admin/ProductController.php` |
| Model | `app/Models/Produk.php` |
| View index | `resources/views/admin/products/index.blade.php` |
| View create | `resources/views/admin/products/create.blade.php` |
| View edit | `resources/views/admin/products/edit.blade.php` |
| Storage gambar | `storage/app/public/products/` |

---

### Admin: Kelola Template

| Komponen | File |
|---|---|
| Route | `routes/web.php` (resource /admin/templates) |
| Controller | `app/Http/Controllers/Admin/TemplateController.php` |
| Model | `app/Models/Template.php` |
| View | `resources/views/admin/templates/` |
| Storage | `storage/app/public/templates/` |

---

### Admin: Kelola Pesanan & Review Desain

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET/PATCH /admin/orders*) |
| Controller | `app/Http/Controllers/Admin/OrderController.php` |
| Model | `app/Models/Order.php` |
| Model | `app/Models/OrderDetail.php` |
| Model | `app/Models/Desain.php` |
| View list | `resources/views/admin/orders/index.blade.php` |
| View detail | `resources/views/admin/orders/show.blade.php` |

---

### Admin: Dashboard

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET /admin/dashboard) |
| Controller | `app/Http/Controllers/Admin/DashboardController.php` |
| View | `resources/views/admin/dashboard.blade.php` |

---

### Owner: Laporan

| Komponen | File |
|---|---|
| Route | `routes/web.php` (GET /admin/reports) |
| Controller | `app/Http/Controllers/Owner/ReportController.php` |
| Model | `app/Models/Order.php` |
| Model | `app/Models/Desain.php` |
| Model | `app/Models/OrderDetail.php` |
| View | `resources/views/owner/reports/index.blade.php` |

---

### Database Migrations (Urutan)

| File | Fungsi |
|---|---|
| `0001_01_01_000000_create_users_table.php` | Tabel users (default Laravel, lalu dihapus) |
| `2026_03_09_053259_create_admins_table.php` | Tabel admins |
| `2026_03_09_053259_create_customers_table.php` | Tabel customers |
| `2026_03_09_053300_create_templates_table.php` | Tabel templates |
| `2026_03_09_053301_create_desains_table.php` | Tabel desains (awal) |
| `2026_03_09_053302_create_orders_table.php` | Tabel orders |
| `2026_03_09_053302_create_produks_table.php` | Tabel produks |
| `2026_03_09_053303_create_order_details_table.php` | Tabel order_details |
| `2026_03_10_011515_add_warna_baju_to_desains_table.php` | Tambah warna_baju |
| `2026_03_28_001345_add_back_design_columns_to_desains_table.php` | Tambah kolom belakang |
| `2026_03_29_060511_create_carts_table.php` | Tabel carts |
| `2026_03_29_060521_add_revision_columns_to_order_details_table.php` | Tambah status_desain |
| `2026_03_29_060529_add_bukti_pembayaran_to_orders_table.php` | Tambah bukti_pembayaran |
| `2026_04_03_081504_update_product_types_and_processing_options.php` | Update tipe produk |
| `2026_04_03_082027_add_tipe_proses_to_carts_table.php` | Tambah tipe_proses ke carts |
| `2026_04_08_044525_add_left_right_designs_to_desains_table.php` | Tambah kiri/kanan |
| `2026_04_18_062456_add_remember_token_to_admins_and_customers_table.php` | remember_token |
| `2026_04_28_041406_add_raw_assets_to_desains_table.php` | Tambah raw_assets |
| `2026_04_29_080000_add_tipe_produk_to_produks_table.php` | Tambah tipe_produk |
| `2026_05_16_185954_remove_bordir_columns_from_multiple_tables.php` | Hapus kolom bordir |
| `2026_05_18_063300_simplify_database_schema_remove_topi_and_left_right_designs.php` | Simplifikasi |
| `2026_05_25_050000_add_detail_sablon_and_update_jenis_produk.php` | Tambah detail_sablon |
| `2026_07_29_054230_make_kategori_nullable_in_templates_table.php` | Kategori nullable |
| `2026_08_01_224400_cleanup_database.php` | Bersihkan tabel & kolom tidak terpakai |
| `2026_08_05_062013_remove_dimension_columns_from_desains_table.php` | Hapus kolom cm |
| `2026_08_07_233031_restructure_business_flow_tables.php` | Tambah payment_status, parent_id, update enum |
| `2026_08_08_145954_add_canvas_data_to_desains_table.php` | Tambah canvas_front/back/left/right |

---

### Config & Setup Files

| File | Fungsi |
|---|---|
| `composer.json` | PHP dependencies |
| `package.json` | Node.js dependencies |
| `vite.config.js` | Vite bundler config |
| `tailwind.config.js` | TailwindCSS config |
| `postcss.config.js` | PostCSS config |
| `.env` | Environment variables (lokal, tidak di-commit) |
| `routes/web.php` | Semua web routes utama |
| `routes/auth.php` | Auth routes |
| `routes/settings.php` | Profile/settings routes |
| `config/auth.php` | Guard dan provider configuration |

---

### Legacy / Tidak Digunakan

| File | Status |
|---|---|
| `resources/views/customer/designs/editor_old.blade.php` | Legacy (81KB), tidak digunakan |
| `app/Livewire/Actions/` | Kosong, tidak digunakan |
| `app/Actions/` | Kosong, tidak digunakan |
| `app/Concerns/` | Kosong, tidak digunakan |
| `app/Models/User.php` | Ada tapi tidak digunakan dalam bisnis utama (tabel users dihapus) |
| `models/` | Folder di root project (isi belum diaudit) |
| `scripts/` | Folder di root project (isi belum diaudit) |
