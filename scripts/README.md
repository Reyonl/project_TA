# Mockup Processing Scripts

Folder ini berisi *script* utilitas internal untuk mempermudah pengembangan *project*.

## `process_mockup.py`
Ini adalah *script* standar pamungkas yang digunakan untuk membersihkan *background* dari gambar mentah (*mockup*) baju AI yang baru ditambahkan. *Script* ini menggunakan logika tingkat lanjut (AI Segment Anything Model + OpenCV Contour Hole-Filling) untuk menjamin hasil yang **100% sempurna**, bebas *checkerboard*, bertepi mulus, tanpa bagian dalam baju/label yang berlubang/hilang warna aslinya.

### Persyaratan / Requirements:
*Script* ini membutuhkan instalasi Python dan beberapa *library*. Jika belum terinstall, jalankan:
```bash
pip install opencv-python numpy rembg[onnxruntime]
```

### Cara Penggunaan (Command Line):

Gunakan terminal/CMD, arahkan ke *root directory project* Laravel ini, lalu jalankan perintah berikut:

```bash
# Format Dasar
python scripts/process_mockup.py <path_gambar_mentah> <path_gambar_hasil>

# Contoh Penggunaan untuk Baju Baru (misal: Jaket)
# 1. Pastikan Anda memiliki gambar mentah (misal: jaket.png)
# 2. Jalankan script ini untuk memproses dan menyimpannya ke folder public:
python scripts/process_mockup.py "path/to/gambar_mentah_jaket.png" "public/images/mockups/jaket.png"
```

**Keterangan Cara Kerja Script**:
1. Mengambil saluran warna asli (BGR) dari gambar mentah untuk diubah ke Grayscale, sehingga tekstur, bayangan asli, dan detail label di bagian dalam baju tidak akan rusak.
2. Meminta bantuan `rembg` (dengan model raksasa `sam` / Segment Anything Model) untuk menelusuri pinggiran baju secara presisi (anti-aliasing).
3. Karena AI seringkali keliru menganggap label kerah bagian dalam sebagai *background* sehingga ikut terhapus, *script* ini menggunakan deteksi `Contour` OpenCV untuk menemukan dan menambal lubang tersebut, menggabungkannya kembali menjadi satu kesatuan (*solid*).
4. Hasil akhirnya berupa gambar PNG *transparent* ber-template *grayscale* murni yang siap merespons perubahan kode HEX warna di dalam Canvas Editor web.
