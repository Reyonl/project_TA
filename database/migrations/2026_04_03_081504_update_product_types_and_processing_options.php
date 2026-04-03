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
        // Update Produks table
        Schema::table('produks', function (Blueprint $table) {
            // Changing enum values in Laravel/MySQL usually requires a raw statement or Doctrine, 
            // but for simplicity here we'll use raw DB if needed, or just add a boolean for bordir support.
            $table->boolean('tersedia_bordir')->default(false)->after('jenis_produk');
        });

        // Use raw SQL to update enum values for compatibility across different environments
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie', 'topi', 'polo', 'seragam') NOT NULL DEFAULT 'kaos'");

        // Update Order Details table
        Schema::table('order_details', function (Blueprint $table) {
            $table->enum('tipe_proses', ['sablon', 'bordir'])->default('sablon')->after('status_desain');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('tersedia_bordir');
        });
        DB::statement("ALTER TABLE produks MODIFY COLUMN jenis_produk ENUM('kaos', 'hoodie') NOT NULL DEFAULT 'kaos'");

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('tipe_proses');
        });
    }
};
