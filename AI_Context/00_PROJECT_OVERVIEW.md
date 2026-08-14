# 00 — PROJECT OVERVIEW

> **Source of Truth:** Source code aktual per audit tanggal 2026-08-11

---

## Tujuan Project

Aplikasi web pemesanan **sablon pakaian custom** berbasis Laravel.  
Customer dapat mendesain sendiri pakaian (kaos, hoodie, polo) menggunakan canvas editor berbasis Fabric.js, kemudian melakukan pemesanan, pembayaran, dan tracking status pesanan secara online.

---

## Masalah yang Diselesaikan

- Proses pemesanan sablon custom yang sebelumnya manual dan tidak terstandarisasi
- Tidak ada sarana digital bagi customer untuk mendesain sendiri tanpa keahlian grafis
- Tidak ada sistem tracking status pesanan dan review desain yang terstruktur
- Tidak ada laporan penjualan yang terintegrasi untuk pemilik usaha

---

## Nama Aplikasi

**DailyCo** (berdasarkan nama file CSV ekspor: `Laporan_Penjualan_DailyCo_...`)

---

## Scope Aplikasi

- Aplikasi web berbasis Laravel + Livewire (Flux/Blaze kit)
- Single server (tidak microservices)
- Multi-guard authentication (Customer + Admin/Owner)
- Canvas editor sisi client (JavaScript/Fabric.js)
- Storage lokal (Laravel Storage disk 'public')
- Tidak ada payment gateway terintegrasi (manual upload bukti)
- Tidak ada real-time notification (tidak ada WebSocket/Pusher)
- Tidak ada email notification (tidak ada mail driver teraktif)

---

## Aktor (Actor)

| Aktor | Deskripsi | Auth Guard |
|---|---|---|
| **Customer** | Pengguna akhir yang melakukan pemesanan | `auth:customer` → tabel `customers` |
| **Admin** | Pengelola operasional (produk, template, order, desain) | `auth:admin` dengan role `admin` |
| **Owner** | Pemilik usaha, hanya akses laporan | `auth:admin` dengan role `owner` |
| **Guest** | Pengunjung tanpa login, bisa lihat katalog | - |

---

## Fitur Utama (CONFIRMED dari source code)

### Customer
1. **Registrasi & Login** — guard `customer`, tabel `customers`
2. **Katalog Produk** — lihat produk publik di landing page
3. **Design Editor (Canvas)** — Fabric.js wizard multi-step (4 atau 6 step tergantung jenis produk)
4. **Keranjang Belanja** — add to cart setelah simpan desain, atau pesan langsung (ready-made)
5. **Checkout** — pilih item dari cart, upload bukti bayar pada tahap terpisah
6. **Riwayat Pesanan** — list & detail order dengan status tracking
7. **Upload Bukti Pembayaran** — upload setelah order dalam status `pending_payment`
8. **Revisi Desain** — kembali ke editor untuk merevisi desain yang ditolak admin

### Admin
1. **Dashboard** — ringkasan statistik
2. **Kelola Produk** — CRUD (kaos, hoodie, polo; tipe kustom/jadi)
3. **Kelola Template** — CRUD aset desain yang bisa dipilih customer di editor
4. **Kelola Pesanan** — lihat list & detail order, update status order
5. **Review Desain** — approve/revisi per item desain dalam order
6. **Verifikasi Pembayaran** — approve/reject bukti pembayaran

### Owner
1. **Laporan Penjualan** — filter tanggal, grafik, tabel transaksi, export CSV

---

## Batasan Sistem (CONFIRMED)

- Produk terbatas: **kaos, hoodie, polo** (jenis `topi` dan `seragam` telah dihapus dari validasi aktual, meskipun ada referensi `seragam` di canvas editor)
- Canvas editor menggunakan `jenis_produk` untuk menentukan jumlah step (kaos/hoodie = 6 step, lainnya = 4 step)
- Tidak ada notifikasi email/push
- Storage via PHP fallback route (bukan symlink), mengindikasikan deployment di shared hosting
- Tidak ada paginasi pada daftar order admin
- Debug routes masih aktif di production (`/debug-storage`)

---

## Konteks Aplikasi

Project ini merupakan **skripsi/tugas akhir** (berdasarkan nama folder `percobaanskripsi_1`, folder `docs/`, dan file `Bab_5_Kesimpulan_Saran.md` di dalam docs).  
Dibuat dengan Laravel Livewire Starter Kit (Flux/Blaze).
