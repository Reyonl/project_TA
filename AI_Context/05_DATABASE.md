# 05 — DATABASE

> **Source of Truth:** Migration files aktual (31 file)  
> Schema.sql lama dijadikan referensi tapi bukan sumber kebenaran.  
> Struktur final berdasarkan urutan migration yang telah dijalankan.

---

## Tabel-Tabel Aktual

### 1. `customers`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_customer` | bigint PK auto-increment | |
| `nama_customer` | varchar | |
| `email` | varchar unique | |
| `password` | varchar hashed | |
| `no_hp` | varchar nullable | |
| `alamat` | text nullable | |
| `remember_token` | varchar nullable | Ditambahkan migration 2026-04-18 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Customer extends Authenticatable`  
**Guard:** `customer`

---

### 2. `admins`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_admin` | bigint PK auto-increment | |
| `nama_admin` | varchar | |
| `email` | varchar unique | |
| `password` | varchar hashed | |
| `role` | varchar | Nilai: `admin`, `owner` |
| `remember_token` | varchar nullable | Ditambahkan migration 2026-04-18 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Admin extends Authenticatable`  
**Guard:** `admin`

---

### 3. `produks`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_produk` | bigint PK auto-increment | |
| `nama_produk` | varchar | Contoh: "Kaos Pendek", "Hoodie Zipper" |
| `jenis_produk` | varchar/enum | Nilai aktual: kaos, hoodie, polo (seragam ada di canvas tapi tidak di validasi) |
| `harga_dasar` | decimal | Harga satuan produk dalam Rupiah |
| `deskripsi` | text nullable | |
| `tipe_produk` | varchar | Nilai: `kustom`, `jadi` |
| `gambar_produk` | varchar nullable | Path relatif ke storage/app/public/ |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Produk`  
**Note:** Kolom `tipe_proses` (sablon/express) pernah ditambahkan lalu dihapus (migration 2026-04-03, 2026-05-16)

---

### 4. `templates`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_template` | bigint PK auto-increment | |
| `id_admin` | bigint FK → admins | Admin yang upload |
| `nama_template` | varchar | |
| `file_template` | varchar | Path ke storage/app/public/templates/ |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Note:** Kolom `kategori` pernah ada lalu dihapus (migration 2026-08-01). Kolom `kategori` juga dibuat nullable via migration 2026-07-29 sebelum akhirnya dihapus.

**Model:** `App\Models\Template`  
**Relationship:** belongsTo Admin, hasMany Desain

---

### 5. `desains`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_desain` | bigint PK auto-increment | |
| `parent_id` | bigint nullable FK → desains.id_desain | Untuk versioning revisi (set null on delete) |
| `id_customer` | bigint FK → customers | |
| `id_template` | bigint nullable FK → templates | Template yang digunakan (opsional) |
| `file_desain` | varchar | Path PNG front desain |
| `file_desain_belakang` | varchar nullable | Path PNG back desain |
| `file_desain_kiri` | varchar nullable | Path PNG left desain |
| `file_desain_kanan` | varchar nullable | Path PNG right desain |
| `warna_baju` | varchar nullable | Hex color atau nama warna (e.g. '#ffffff', 'White') |
| `canvas_front` | longText nullable | JSON Fabric.js state depan |
| `canvas_back` | longText nullable | JSON Fabric.js state belakang |
| `canvas_left` | longText nullable | JSON Fabric.js state kiri |
| `canvas_right` | longText nullable | JSON Fabric.js state kanan |
| `harga_desain` | decimal nullable | Biaya sablon (saat ini selalu 0) |
| `raw_assets` | json nullable | Array path file gambar upload customer |
| `detail_sablon` | text nullable | Deskripsi objek sablon (teks, stiker, dll.) |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Desain`  
**Model Cast:** `raw_assets` → array

**Kolom yang pernah ada lalu dihapus (migration history):**
- `tanggal_upload` — dihapus 2026-08-01
- `lebar_cm`, `tinggi_cm`, `lebar_cm_belakang`, `tinggi_cm_belakang` — dihapus 2026-08-05
- `lebar_cm_kiri`, `tinggi_cm_kiri`, `lebar_cm_kanan`, `tinggi_cm_kanan` — dihapus 2026-08-05

---

### 6. `carts`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_cart` | bigint PK auto-increment | |
| `id_customer` | bigint FK → customers | |
| `id_produk` | bigint FK → produks | |
| `id_desain` | bigint nullable FK → desains | Null jika ready-made |
| `quantity` | int | Default: 1 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Cart`  
**Note:** Kolom `tipe_proses` pernah ditambahkan (migration 2026-04-03) tapi tidak ada di model `$fillable`. Status keberadaan di DB final perlu dikonfirmasi.

---

### 7. `orders`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_order` | bigint PK auto-increment | |
| `id_customer` | bigint FK → customers | |
| `tanggal_order` | datetime | Waktu order dibuat |
| `status_order` | enum | `reviewing`, `pending_payment`, `processing`, `completed`, `cancelled` |
| `payment_status` | enum | `unpaid`, `awaiting_payment`, `awaiting_verification`, `paid`, `failed` |
| `total_harga` | decimal | Total keseluruhan order |
| `bukti_pembayaran` | varchar nullable | Path ke storage/app/public/bukti_pembayaran/ |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\Order`  
**Note:** Kolom `payment_status` ditambahkan migration 2026-08-07. Model belum diupdate `$fillable` untuk `payment_status` → perlu `payment_status` di create() explicitly.

---

### 8. `order_details`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_order_detail` | bigint PK auto-increment | |
| `id_order` | bigint FK → orders | |
| `id_produk` | bigint FK → produks | |
| `id_desain` | bigint nullable FK → desains | Null = produk ready-made |
| `quantity` | int | |
| `harga_produk` | decimal | Snapshot harga produk saat order |
| `harga_desain` | decimal | Snapshot biaya desain saat order |
| `subtotal` | decimal | (harga_produk + harga_desain) * quantity |
| `status_desain` | enum | `pending`, `revision_required`, `approved` |
| `catatan_admin` | text nullable | Catatan admin untuk revisi/approval |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Model:** `App\Models\OrderDetail`

---

### Tabel Sistem (Laravel)
| Tabel | Status |
|---|---|
| `users` | DIHAPUS pada migration cleanup 2026-08-01 (Model User.php masih ada tapi tidak digunakan bisnis utama) |
| `cache` | Ada (migration default Laravel) |
| `sessions` | Ada (migration default Laravel) |
| `failed_jobs` | DIHAPUS migration 2026-08-01 |
| `jobs` | DIHAPUS migration 2026-08-01 |
| `password_reset_tokens` | DIHAPUS migration 2026-08-01 |
| `admins_and_customers` | Kolom remember_token ditambahkan migration 2026-04-18 |

---

## Entity Relationship Diagram (Tekstual)

```
customers ──< carts
customers ──< orders
customers ──< desains

admins ──< templates
templates ──< desains (optional)

desains <── cart.id_desain (nullable)
desains <── order_details.id_desain (nullable)
desains ──o desains.parent_id (self-referential, versioning)

orders ──< order_details
produks ──< order_details
produks ──< carts
```

---

## Data Flow

1. Customer desain → `desains` record dibuat → `carts` record dibuat (otomatis)
2. Customer checkout → `orders` header dibuat → `order_details` dibuat dari cart items → cart items dihapus
3. Admin review → `order_details.status_desain` diupdate → jika semua approved → `orders.status_order` auto-update
4. Customer upload bukti → `orders.bukti_pembayaran` & `orders.payment_status` diupdate
5. Admin verifikasi → `orders.payment_status` & `orders.status_order` diupdate
6. Revisi: desain BARU dibuat (`parent_id` ke desain lama) → `order_details.id_desain` diupdate ke desain baru

---

## Catatan Penting Database

- **Tidak ada soft delete** di model manapun (confirmed dari model files)
- **Tidak ada index** yang dikonfirmasi dari migrasi selain FK dan PK
- **FK constraint** ada: orders→customers, order_details→orders, order_details→produks, order_details→desains, desains→customers, desains→templates, desains→desains (parent)
- Kolom `payment_status` TIDAK ada di `Order.$fillable` — create() di CheckoutController menggunakan kolom ini secara langsung (ini bisa bermasalah jika mass assignment protection strict)
