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
            $table->dropColumn([
                'lebar_cm', 'tinggi_cm',
                'lebar_cm_belakang', 'tinggi_cm_belakang',
                'lebar_cm_kiri', 'tinggi_cm_kiri',
                'lebar_cm_kanan', 'tinggi_cm_kanan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desains', function (Blueprint $table) {
            $table->decimal('lebar_cm', 5, 2)->nullable()->after('file_desain_kanan');
            $table->decimal('tinggi_cm', 5, 2)->nullable()->after('lebar_cm');
            $table->decimal('lebar_cm_belakang', 5, 2)->nullable()->after('tinggi_cm');
            $table->decimal('tinggi_cm_belakang', 5, 2)->nullable()->after('lebar_cm_belakang');
            $table->decimal('lebar_cm_kiri', 5, 2)->nullable()->after('tinggi_cm_belakang');
            $table->decimal('tinggi_cm_kiri', 5, 2)->nullable()->after('lebar_cm_kiri');
            $table->decimal('lebar_cm_kanan', 5, 2)->nullable()->after('tinggi_cm_kiri');
            $table->decimal('tinggi_cm_kanan', 5, 2)->nullable()->after('lebar_cm_kanan');
        });
    }
};
