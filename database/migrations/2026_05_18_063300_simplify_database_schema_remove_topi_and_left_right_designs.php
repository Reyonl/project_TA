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
        // Update data yang memiliki jenis produk di luar kaos dan hoodie agar menjadi kaos secara default
        DB::table('produks')
            ->whereNotIn('jenis_produk', ['kaos', 'hoodie'])
            ->update(['jenis_produk' => 'kaos']);

        // 1. Sederhanakan ENUM jenis_produk
        // Mengubah nilai ENUM untuk membuang 'topi', 'polo', 'seragam' yang tidak relevan lagi
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie') NOT NULL DEFAULT 'kaos'");

        // 2. Hapus kolom desain sisi kiri dan kanan pada tabel desains
        Schema::table('desains', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('desains', 'file_desain_kiri')) {
                $columnsToDrop[] = 'file_desain_kiri';
            }
            if (Schema::hasColumn('desains', 'lebar_cm_kiri')) {
                $columnsToDrop[] = 'lebar_cm_kiri';
            }
            if (Schema::hasColumn('desains', 'tinggi_cm_kiri')) {
                $columnsToDrop[] = 'tinggi_cm_kiri';
            }
            if (Schema::hasColumn('desains', 'file_desain_kanan')) {
                $columnsToDrop[] = 'file_desain_kanan';
            }
            if (Schema::hasColumn('desains', 'lebar_cm_kanan')) {
                $columnsToDrop[] = 'lebar_cm_kanan';
            }
            if (Schema::hasColumn('desains', 'tinggi_cm_kanan')) {
                $columnsToDrop[] = 'tinggi_cm_kanan';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ENUM jenis_produk seperti sebelumnya (atau dengan asumsi opsi sebelumnya)
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie', 'topi', 'polo', 'seragam') NOT NULL DEFAULT 'kaos'");

        // Tambahkan kembali kolom desain sisi kiri dan kanan
        Schema::table('desains', function (Blueprint $table) {
            $table->string('file_desain_kiri')->nullable()->after('tinggi_cm_belakang');
            $table->decimal('lebar_cm_kiri', 8, 2)->nullable()->after('file_desain_kiri');
            $table->decimal('tinggi_cm_kiri', 8, 2)->nullable()->after('lebar_cm_kiri');
            
            $table->string('file_desain_kanan')->nullable()->after('tinggi_cm_kiri');
            $table->decimal('lebar_cm_kanan', 8, 2)->nullable()->after('file_desain_kanan');
            $table->decimal('tinggi_cm_kanan', 8, 2)->nullable()->after('lebar_cm_kanan');
        });
    }
};
