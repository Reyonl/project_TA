<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update table produks
        Schema::table('produks', function (Blueprint $table) {
            $table->enum('tipe_produk', ['kustom', 'jadi'])->default('kustom')->after('tersedia_bordir');
            $table->string('gambar_produk')->nullable()->after('tipe_produk');
        });

        // 2. Update table carts
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_desain')->nullable()->change();
        });

        // 3. Update table order_details
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id_desain')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('tipe_produk');
            $table->dropColumn('gambar_produk');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_desain')->nullable(false)->change();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id_desain')->nullable(false)->change();
        });
    }
};
