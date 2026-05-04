# Dokumentasi Activity Diagram - Sistem Mockup Desain Sablon Baju

Dokumen ini berisi rancangan lengkap **Activity Diagram** berdasarkan *Use Case Diagram* untuk tiga aktor: **Customer**, **Admin**, dan **Owner**. Setiap diagram dilengkapi dengan penjelasan alur (Swimlane) dan representasi visual dalam format *Mermaid Flowchart*.

---

## 👨‍💻 AKTOR 1: CUSTOMER

### 1. Activity Diagram: Registrasi Akun
**Deskripsi Alur:**
*   **Customer:** Membuka halaman registrasi, mengisi form data diri (nama, email, password), dan menekan tombol daftar.
*   **Sistem:** Memvalidasi inputan. Jika data tidak valid (email sudah terdaftar/kosong), sistem menampilkan pesan error. Jika valid, sistem menyimpan data ke database dan mengarahkan pengguna ke halaman login.

```mermaid
flowchart TD
    subgraph Customer
        A((Start)) --> B[Buka Halaman Registrasi]
        B --> C[Isi Form Registrasi]
        C --> D[Klik Tombol Daftar]
    end
    subgraph Sistem
        D --> E{Validasi Data?}
        E -- Tidak Valid --> F[Tampilkan Pesan Error]
        E -- Valid --> G[Simpan Data ke Database]
        G --> H[Notifikasi Sukses & Arahkan Login]
    end
    F --> C
    H --> I((End))
```

### 2. Activity Diagram: Membuat Desain Custom *(Include: Pilih Template Mentahan)*
**Deskripsi Alur:**
*   **Customer:** Membuka menu desain, memilih template baju mentahan, lalu memanipulasi desain (menambah teks/stiker) di kanvas, dan menyimpan hasil desain.
*   **Sistem:** Menampilkan pilihan template, memproses file desain (merender ukuran/posisi), memvalidasi kelengkapan, dan menyimpannya di database (tabel `Desain`).

```mermaid
flowchart TD
    subgraph Customer
        A((Start)) --> B[Pilih Menu Desain Custom]
        C[Pilih Template Baju Mentahan] --> D[Masuk ke Editor Kanvas]
        D --> E[Modifikasi Desain Tambah Teks / Stiker]
        E --> F[Klik Simpan Desain]
    end
    subgraph Sistem
        B --> B1[Tampilkan Daftar Template Mentahan]
        B1 --> C
        F --> G{Validasi Objek Desain?}
        G -- Kanvas Kosong --> H[Tampilkan Error]
        G -- Valid --> I[Generate & Simpan Desain ke DB]
        I --> J[Tampilkan Pesan Sukses & Simpan ke Keranjang]
    end
    H --> E
    J --> Z((End))
```

### 3. Activity Diagram: Kelola Keranjang *(Extend: Hapus Item Keranjang)*
**Deskripsi Alur:**
*   **Customer:** Membuka keranjang belanja, melihat daftar produk/desain. Customer dapat memilih untuk menghapus item (extend) atau lanjut ke *checkout*.
*   **Sistem:** Menampilkan data keranjang dari *session* atau database. Jika ada aksi hapus, sistem menghapus relasi dari tabel `Cart` dan merefresh tampilan keranjang.

```mermaid
flowchart TD
    subgraph Customer
        A((Start)) --> B[Buka Keranjang Belanja]
        B1[Lihat Daftar Item] --> C{Pilih Aksi?}
        C -- Hapus Item --> D[Klik Ikon Hapus pada Item]
        C -- Checkout --> K[Lanjut ke Checkout Pesanan]
    end
    subgraph Sistem
        B --> B1
        D --> E[Proses Hapus Data di Tabel Cart]
        E --> F[Update Tampilan Keranjang]
    end
    F --> B1
    K --> Z((End))
```

### 4. Activity Diagram: Checkout Pesanan *(Include: Lakukan Pembayaran)*
**Deskripsi Alur:**
*   **Customer:** Memulai proses checkout, memilih kurir, membuat pesanan, mentransfer uang, dan mengupload bukti pembayaran.
*   **Sistem:** Membuat rekam jejak pesanan (`Order`), mengkalkulasi total harga, dan menunggu file upload bukti transfer. Jika bukti valid, status pesanan menjadi `Menunggu Konfirmasi`.

```mermaid
flowchart TD
    subgraph Customer
        A((Start)) --> B[Klik Checkout di Keranjang]
        C[Pilih Ekspedisi / Kurir] --> D[Klik Buat Pesanan]
        E[Lakukan Transfer Bank] --> F[Upload Bukti Pembayaran]
    end
    subgraph Sistem
        B --> B1[Tampilkan Form Checkout & Ringkasan]
        B1 --> C
        D --> D1[Simpan Data Order & Detail Status Pending]
        D1 --> E
        F --> G{Cek Tipe/Ukuran File?}
        G -- Tidak Valid --> I[Tampilkan Error Upload]
        G -- Valid --> H[Simpan File & Update Status Verifikasi]
    end
    I --> F
    H --> Z((End))
```

### 5. Activity Diagram: Lacak Pesanan
**Deskripsi Alur:**
*   **Customer:** Membuka menu riwayat transaksi, memilih salah satu transaksi untuk melihat pergerakan status (Pending, Diproses, Dikirim, Selesai).
*   **Sistem:** Mengambil data relasional `Order` dan mengembalikan view status pesanan terkini.

```mermaid
flowchart TD
    subgraph Customer
        A((Start)) --> B[Buka Menu Riwayat Pesanan]
        C[Pilih Detail Pesanan] --> D[Lihat Status & Resi Pesanan]
    end
    subgraph Sistem
        B --> B1[Tarik Data Order dari Database]
        B1 --> C
        D --> Z((End))
    end
```

---

## 🛡️ AKTOR 2: ADMIN

### 6. Activity Diagram: Login (Umum untuk Semua Aktor)
**Deskripsi Alur:**
*   **Aktor:** Masuk ke halaman login, mengisi email dan password.
*   **Sistem:** Mengotentikasi ke database, memvalidasi role (Admin, Customer, Owner). Jika gagal, lempar kembali ke form login dengan alert error.

```mermaid
flowchart TD
    subgraph Aktor
        A((Start)) --> B[Buka Halaman Login]
        C[Input Email dan Password] --> D[Klik Tombol Login]
    end
    subgraph Sistem
        B --> B1[Tampilkan Form Login]
        B1 --> C
        D --> E{Otentikasi Kredensial?}
        E -- Salah --> F[Tampilkan Pesan Kredensial Invalid]
        E -- Benar --> G[Cek Role Hak Akses]
        G --> H[Arahkan ke Dashboard Masing-masing Role]
    end
    F --> C
    H --> Z((End))
```

### 7. Activity Diagram: Kelola Data Produk
**Deskripsi Alur:**
*   **Admin:** Mengakses modul katalog produk, memiliki kapabilitas untuk *Create*, *Update*, atau *Delete* jenis sablon / barang.
*   **Sistem:** Merespon request CRUD dan memutakhirkan tabel `Produk` di database.

```mermaid
flowchart TD
    subgraph Admin
        A((Start)) --> B[Buka Modul Kelola Produk]
        C{Pilih Aksi?} -->|Tambah| D[Isi Form Produk Baru]
        C -->|Edit| E[Ubah Data Form Produk]
        C -->|Hapus| F[Konfirmasi Hapus Data]
        D --> G[Klik Simpan]
        E --> G
    end
    subgraph Sistem
        B --> B1[Tampilkan Tabel Master Produk]
        B1 --> C
        F --> H[Eksekusi Hapus di Database]
        G --> I{Validasi Input Produk?}
        I -- Gagal --> J[Tampilkan Error Form]
        I -- Berhasil --> K[Update / Insert Database]
    end
    H --> B1
    J --> D
    K --> B1
```

### 8. Activity Diagram: Kelola Template Desain
**Deskripsi Alur:**
*   **Admin:** Menambahkan file *mockup* baju mentahan kosong.
*   **Sistem:** Mengunggah file ke direktori `/public` dan mencatat lokasinya ke tabel `Template`.

```mermaid
flowchart TD
    subgraph Admin
        A((Start)) --> B[Buka Menu Template Desain]
        C{Pilih Aksi?} -->|Tambah| D[Pilih Gambar Baju Kosong & Kategori]
        C -->|Hapus| E[Pilih Template & Klik Hapus]
        D --> F[Simpan Template]
    end
    subgraph Sistem
        B --> B1[Load Data Tabel Template]
        B1 --> C
        E --> G[Hapus File di Storage & Hapus Baris Database]
        F --> H{Ekstensi & Ukuran File Valid?}
        H -- Tidak --> I[Tampilkan Error Validation]
        H -- Ya --> J[Upload Gambar & Catat Path ke Database]
    end
    G --> B1
    I --> D
    J --> B1
```

### 9. Activity Diagram: Review Desain Order *(Extend: Beri Catatan Revisi)*
**Deskripsi Alur:**
*   **Admin:** Melihat desain *custom* yang disubmit Customer. Memutuskan apakah kualitas desain bisa dicetak. Jika desain pecah/melanggar aturan, Admin memberi pesan revisi (Extend). Jika aman, lanjut diproses.
*   **Sistem:** Mengubah *Status Desain* di tabel `OrderDetail` dan merekam catatan penolakan ke log/tabel jika terjadi revisi.

```mermaid
flowchart TD
    subgraph Admin
        A((Start)) --> B[Buka Daftar Pesanan Masuk]
        B1[Lihat Resolusi & Layout Desain] --> C{Review Hasil Desain?}
        C -- Desain Cacat/Revisi --> D[Isi Form Catatan Revisi]
        C -- Desain Sesuai --> E[Setujui Desain]
        D --> F[Kirim Catatan]
    end
    subgraph Sistem
        B --> B1
        F --> G[Update Status jadi Perlu Revisi & Notif ke Customer]
        E --> H[Update Status Order jadi Sedang Diproses]
    end
    G --> Z((End))
    H --> Z
```

### 10. Activity Diagram: Kelola Pesanan *(Extend: Batalkan Pesanan)*
**Deskripsi Alur:**
*   **Admin:** Memeriksa mutasi bank untuk pesanan baru. Jika transfer palsu atau Customer membatalkan, pesanan ditolak (Extend). Jika benar, masuk ke fase produksi & resi di-input.
*   **Sistem:** Memanajemen perubahan `status_order` hingga order diselesaikan.

```mermaid
flowchart TD
    subgraph Admin
        A((Start)) --> B[Buka Menu Manajemen Pesanan]
        B1[Cek Bukti Pembayaran Customer] --> C{Validitas Pembayaran?}
        C -- Transfer Fiktif/Expired --> D[Tolak & Batalkan Pesanan]
        C -- Valid --> E[Verifikasi & Lanjut Tahap Produksi]
        E --> F[Pesanan Selesai Produksi & Input Nomor Resi]
    end
    subgraph Sistem
        B --> B1
        D --> D1[Update Status Batal]
        E --> E1[Ubah Status Diproses]
        F --> F1[Ubah Status Dikirim & Kirim Email Resi]
    end
    D1 --> Z((End))
    E1 --> Z
    F1 --> Z
```

---

## 👔 AKTOR 3: OWNER

### 11. Activity Diagram: Lihat Laporan Penjualan *(Include: Filter Periode)*
**Deskripsi Alur:**
*   **Owner:** Mengecek kinerja bisnis dengan memfilter rentang tanggal (tanggal awal - akhir). Laporan kemudian dapat dicetak (PDF).
*   **Sistem:** Melakukan kalkulasi agregrasi keuntungan (SUM, COUNT) pada tabel `Order` & `OrderDetail` sesuai parameter tanggal.

```mermaid
flowchart TD
    subgraph Owner
        A((Start)) --> B[Buka Modul Laporan Penjualan]
        C[Input Rentang Tanggal Mulai - Akhir] --> D[Klik Filter / Terapkan]
        D1[Analisa Tabel & Grafik Pendapatan] --> E[Export Data ke PDF]
    end
    subgraph Sistem
        B --> B1[Tampilkan Laporan Rekap Bulan Ini secara Default]
        B1 --> C
        D --> D2[Lakukan Query Query Select By Date]
        D2 --> D1
        E --> F[Generate Dokumen PDF & Trigger Download]
    end
    F --> Z((End))
```
