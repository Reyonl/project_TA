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
        Schema::create('desains', function (Blueprint $table) {
            $table->id('id_desain');
            $table->unsignedBigInteger('id_customer');
            $table->unsignedBigInteger('id_template')->nullable();
            $table->string('file_desain');
            $table->decimal('lebar_cm', 5, 2);
            $table->decimal('tinggi_cm', 5, 2);
            $table->decimal('harga_desain', 10, 2);
            $table->date('tanggal_upload');
            $table->timestamps();
            
            $table->foreign('id_customer')->references('id_customer')->on('customers')->onDelete('cascade');
            $table->foreign('id_template')->references('id_template')->on('templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desains');
    }
};
