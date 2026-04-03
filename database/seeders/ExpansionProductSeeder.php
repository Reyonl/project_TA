<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpansionProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Produk::create([
            'nama_produk' => 'Topi Custom (Premium Cap)',
            'jenis_produk' => 'topi',
            'harga_dasar' => 25000,
            'tersedia_bordir' => true,
            'deskripsi' => 'Topi baseball premium dengan panel depan yang kokoh, sangat cocok untuk bordir logo komunitas atau brand Anda.'
        ]);

        \App\Models\Produk::create([
            'nama_produk' => 'Poloshirt Lacoste Pique',
            'jenis_produk' => 'polo',
            'harga_dasar' => 65000,
            'tersedia_bordir' => true,
            'deskripsi' => 'Kaos kerah dengan bahan Lacoste Pique berkualitas. Nyaman digunakan untuk seragam semi-formal atau event kantor.'
        ]);

        \App\Models\Produk::create([
            'nama_produk' => 'Seragam Kerja Pro-Wear',
            'jenis_produk' => 'seragam',
            'harga_dasar' => 120000,
            'tersedia_bordir' => true,
            'deskripsi' => 'Kemeja seragam kerja dengan bahan American Drill yang kuat dan awet. Ideal untuk bordir nama instansi atau logo perusahaan.'
        ]);
    }
}
