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
        Schema::table('desains', function (Blueprint $table) {
            $table->string('file_desain_kiri')->nullable()->after('tinggi_cm_belakang');
            $table->decimal('lebar_cm_kiri', 8, 2)->nullable()->after('file_desain_kiri');
            $table->decimal('tinggi_cm_kiri', 8, 2)->nullable()->after('lebar_cm_kiri');
            $table->string('file_desain_kanan')->nullable()->after('tinggi_cm_kiri');
            $table->decimal('lebar_cm_kanan', 8, 2)->nullable()->after('file_desain_kanan');
            $table->decimal('tinggi_cm_kanan', 8, 2)->nullable()->after('lebar_cm_kanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desains', function (Blueprint $table) {
            $table->dropColumn([
                'file_desain_kiri', 'lebar_cm_kiri', 'tinggi_cm_kiri',
                'file_desain_kanan', 'lebar_cm_kanan', 'tinggi_cm_kanan'
            ]);
        });
    }
};
