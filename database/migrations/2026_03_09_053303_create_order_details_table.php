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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('id_order_detail');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_desain');
            $table->integer('quantity');
            $table->decimal('harga_produk', 10, 2);
            $table->decimal('harga_desain', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            $table->foreign('id_order')->references('id_order')->on('orders')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produks')->onDelete('cascade');
            $table->foreign('id_desain')->references('id_desain')->on('desains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
