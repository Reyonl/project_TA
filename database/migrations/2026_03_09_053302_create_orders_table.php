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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->unsignedBigInteger('id_customer');
            $table->date('tanggal_order');
            $table->enum('status_order', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');
            $table->decimal('total_harga', 10, 2);
            $table->timestamps();
            
            $table->foreign('id_customer')->references('id_customer')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
