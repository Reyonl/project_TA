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
        Schema::table('produks', function (Blueprint $table) {
            if (Schema::hasColumn('produks', 'tersedia_bordir')) {
                $table->dropColumn('tersedia_bordir');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'tipe_proses')) {
                $table->dropColumn('tipe_proses');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'tipe_proses')) {
                $table->dropColumn('tipe_proses');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
