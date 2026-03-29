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
            $table->string('file_desain_belakang')->nullable()->after('file_desain');
            $table->decimal('lebar_cm_belakang', 5, 2)->nullable()->after('lebar_cm');
            $table->decimal('tinggi_cm_belakang', 5, 2)->nullable()->after('tinggi_cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desains', function (Blueprint $table) {
            $table->dropColumn(['file_desain_belakang', 'lebar_cm_belakang', 'tinggi_cm_belakang']);
        });
    }
};
