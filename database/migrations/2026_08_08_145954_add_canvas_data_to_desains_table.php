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
            $table->longText('canvas_front')->nullable()->after('warna_baju');
            $table->longText('canvas_back')->nullable()->after('canvas_front');
            $table->longText('canvas_left')->nullable()->after('canvas_back');
            $table->longText('canvas_right')->nullable()->after('canvas_left');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desains', function (Blueprint $table) {
            $table->dropColumn(['canvas_front', 'canvas_back', 'canvas_left', 'canvas_right']);
        });
    }
};
