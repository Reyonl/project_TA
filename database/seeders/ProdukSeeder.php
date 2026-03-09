<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produk::create([
            'nama_produk' => 'Kaos Polos Cotton Combed 30s',
            'jenis_produk' => 'kaos',
            'harga_dasar' => 45000.00,
            'deskripsi' => 'Kaos polos bahan cotton combed 30s yang nyaman dan cocok untuk disablon.',
        ]);

        Produk::create([
            'nama_produk' => 'Hoodie Polos Fleece Jumper',
            'jenis_produk' => 'hoodie',
            'harga_dasar' => 85000.00,
            'deskripsi' => 'Hoodie polos jumper bahan fleece tebal premium.',
        ]);
    }
}
