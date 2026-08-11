# 01 — TECH STACK

> **Source of Truth:** `composer.json`, `package.json`, source files aktual  
> Semua versi dikonfirmasi dari file konfigurasi, bukan asumsi.

---

## Backend (PHP/Laravel)

| Teknologi | Versi | Sumber |
|---|---|---|
| PHP | `^8.2` | `composer.json` require |
| Laravel Framework | `^12.0` | `composer.json` require |
| Livewire | `^4.0` | `composer.json` require |
| Livewire Flux | `^2.9.0` | `composer.json` require |
| Livewire Blaze | `^1.0` | `composer.json` require |
| Laravel Fortify | `^1.30` | `composer.json` require |
| Laravel Tinker | `^2.10.1` | `composer.json` require |

### Dev Dependencies Backend
| Teknologi | Versi |
|---|---|
| FakerPHP | `^1.23` |
| Laravel Breeze | `*` (dev) |
| Laravel Pint | `^1.24` (linter) |
| Laravel Sail | `^1.41` |
| PHPUnit | `^11.5.3` |

---

## Frontend (JavaScript/CSS)

| Teknologi | Versi | Sumber |
|---|---|---|
| Vite | `^7.0.4` | `package.json` |
| TailwindCSS | `^4.1.11` (@tailwindcss/vite) | `package.json` |
| TailwindCSS (legacy config) | `^3.1.0` (devDependencies) | `package.json` |
| Alpine.js | `^3.4.2` | `package.json` devDependencies |
| Axios | `^1.7.4` | `package.json` |
| Laravel Vite Plugin | `^2.0` | `package.json` |

> ⚠️ **CATATAN:** `package.json` mengandung dua versi TailwindCSS yang berbeda. `dependencies` menggunakan `@tailwindcss/vite ^4.1.11`, sedangkan `devDependencies` menggunakan TailwindCSS v3. Ini adalah konflik versi yang perlu diperhatikan.

---

## Canvas Editor (JavaScript)

| Teknologi | Sumber | Catatan |
|---|---|---|
| **Fabric.js** (versi tidak diketahui) | `public/js/fabric.min.js` (local bundle, 313KB) | Versi tidak bisa dikonfirmasi dari file minified |
| **fabric-smart-guides.js** | `public/js/fabric-smart-guides.js` (custom, 9.8KB) | Implementasi custom snap guidelines |
| **SweetAlert2** | CDN (`cdn.jsdelivr.net/npm/sweetalert2@11`) | Untuk alert dan konfirmasi |

---

## Database

| Teknologi | Sumber |
|---|---|
| SQLite | `database/database.sqlite` (development) |
| MySQL/MariaDB | `schema.sql` di AI_Context + migrasi aktual |

> **Catatan:** File `.env` menggunakan SQLite untuk development lokal. Production tidak dikonfirmasi.

---

## External APIs / CDN

| Layanan | Kegunaan | Tipe |
|---|---|---|
| **Iconify.design API** | Cari dan ambil SVG ikon untuk stiker editor | External REST API |
| **DiceBear API v8.x** | Generate avatar/emoji untuk stiker editor | External REST API |
| **Google Fonts CDN** | Font: Bebas Neue, Dancing Script, Lobster, Montserrat, Pacifico, Playfair Display, Roboto, Oswald, Anton | CSS CDN |
| **SweetAlert2 CDN** | Alert dialog di editor | JS CDN |

---

## Storage

| Disk | Path | Kegunaan |
|---|---|---|
| `public` disk | `storage/app/public/` | Semua file upload |
| Sub-folder `designs/` | File gambar desain (PNG) hasil export canvas |
| Sub-folder `designs/assets/` | Raw assets (gambar upload customer mentah) |
| Sub-folder `products/` | Gambar produk |
| Sub-folder `bukti_pembayaran/` | Bukti transfer customer |
| `public/images/mockups/` | Gambar mockup baju (tidak di storage, tapi di public) |
| `public/images/` | Logo dan aset statis |

---

## Build Tools

| Teknologi | Versi | Kegunaan |
|---|---|---|
| Vite | `^7.0.4` | Module bundler, dev server |
| PostCSS | `^8.4.31` | CSS processing |
| Autoprefixer | `^10.4.20` | CSS vendor prefix |
| Concurrently | `^9.0.1` | Run multiple dev commands |

---

## Auth System

Menggunakan **Laravel multi-guard authentication**:
- Guard `customer` → tabel `customers`, model `App\Models\Customer`
- Guard `admin` → tabel `admins`, model `App\Models\Admin`
- Guard `web` (Laravel default) → tabel `users`, model `App\Models\User` (ada, tapi tidak aktif digunakan dalam alur bisnis utama)
- **Two Factor Auth** tersedia via `Laravel\Fortify\TwoFactorAuthenticatable` di model `User` (hanya untuk guard web, bukan customer/admin)

---

## Deployment Indicator

Route fallback storage PHP (`Route::get('storage/{path}', ...)`) mengindikasikan project ini dirancang untuk shared hosting tanpa akses symlink `php artisan storage:link`.
