<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan produk yang kategorinya tidak sesuai dengan kaos, hoodie, atau polo
        DB::table('produks')
            ->whereNotIn('jenis_produk', ['kaos', 'hoodie', 'polo'])
            ->delete();

        // 2. Ubah kolom ENUM jenis_produk pada tabel produks menjadi hanya kaos, hoodie, dan polo
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie', 'polo') NOT NULL DEFAULT 'kaos'");

        // 3. Tambahkan kolom detail_sablon pada tabel desains
        Schema::table('desains', function (Blueprint $table) {
            if (!Schema::hasColumn('desains', 'detail_sablon')) {
                $table->text('detail_sablon')->nullable()->after('harga_desain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom ENUM jenis_produk pada tabel produks ke opsi yang lebih lengkap
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie', 'topi', 'polo', 'seragam') NOT NULL DEFAULT 'kaos'");

        // Hapus kolom detail_sablon pada tabel desains
        Schema::table('desains', function (Blueprint $table) {
            if (Schema::hasColumn('desains', 'detail_sablon')) {
                $table->dropColumn('detail_sablon');
            }
        });
    }
};
