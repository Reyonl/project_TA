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
        Schema::table('order_details', function (Blueprint $table) {
            $table->enum('status_desain', ['pending', 'disetujui', 'revisi'])->default('pending')->after('subtotal');
            $table->text('catatan_admin')->nullable()->after('status_desain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['status_desain', 'catatan_admin']);
        });
    }
};
