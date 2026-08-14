<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks untuk melakukan pembersihan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Produk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. KAOS
        Produk::create([
            'nama_produk' => 'Kaos Pendek Cotton Combed 30s',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 42000.00,
            'deskripsi' => 'Bahan Cotton Combed 30s premium. Dingin, menyerap keringat, sangat nyaman dipakai harian.',
        ]);

        Produk::create([
            'nama_produk' => 'Kaos Pendek Cotton Combed 24s',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 52000.00,
            'deskripsi' => 'Bahan Cotton Combed 24s tebal berkualitas tinggi, awet, dan memberikan kenyamanan maksimal.',
        ]);

        Produk::create([
            'nama_produk' => 'Kaos Panjang Cotton',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 72000.00,
            'deskripsi' => 'Bahan katun premium lengan panjang dengan ketebalan sedang yang pas untuk disablon.',
        ]);

        Produk::create([
            'nama_produk' => 'Kaos Panjang Terry',
            'jenis_produk' => 'kaos',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 62000.00,
            'deskripsi' => 'Bahan Baby Terry lengan panjang. Sedikit lebih hangat, sangat lembut, dan tetap menyerap keringat.',
        ]);

        // 2. POLO
        Produk::create([
            'nama_produk' => 'Poloshirt Cotton',
            'jenis_produk' => 'polo',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 82000.00,
            'deskripsi' => 'Kaos kerah bahan katun pique rajutan halus. Terlihat formal dan elegan untuk seragam.',
        ]);

        Produk::create([
            'nama_produk' => 'Poloshirt Polyester',
            'jenis_produk' => 'polo',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 72000.00,
            'deskripsi' => 'Kaos kerah bahan polyester pique yang tahan kusut, kuat, dan sangat tahan lama.',
        ]);

        // 3. HOODIE
        Produk::create([
            'nama_produk' => 'Hoodie Fleece',
            'jenis_produk' => 'hoodie',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 92000.00,
            'deskripsi' => 'Hoodie bahan fleece tebal premium. Bagian dalam berbulu halus, hangat, dan lembut.',
        ]);

        Produk::create([
            'nama_produk' => 'Hoodie Baby Terry',
            'jenis_produk' => 'hoodie',
            'tipe_produk' => 'kustom',
            'harga_dasar' => 82000.00,
            'deskripsi' => 'Hoodie bahan baby terry premium yang tidak terlalu tebal, pas untuk cuaca tropis.',
        ]);
    }
}
