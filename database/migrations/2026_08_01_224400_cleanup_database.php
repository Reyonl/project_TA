<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pembersihan database:
     * 1. Hapus kolom 'kategori' dari templates (tidak pernah digunakan)
     * 2. Hapus kolom 'tanggal_upload' dari desains (redundan dengan created_at)
     * 3. Tambah kembali kolom desain kiri/kanan ke desains (dibutuhkan untuk hoodie)
     * 4. Hapus tabel framework yang tidak digunakan
     */
    public function up(): void
    {
        // 1. Hapus kolom 'kategori' dari templates
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });

        // 2. Hapus kolom 'tanggal_upload' dari desains (redundan dengan created_at)
        Schema::table('desains', function (Blueprint $table) {
            $table->dropColumn('tanggal_upload');
        });

        // 3. Tambah kembali kolom desain kiri/kanan (untuk fitur hoodie samping)
        Schema::table('desains', function (Blueprint $table) {
            $table->string('file_desain_kiri')->nullable()->after('file_desain_belakang');
            $table->string('file_desain_kanan')->nullable()->after('file_desain_kiri');
            $table->decimal('lebar_cm_kiri', 5, 2)->nullable()->after('lebar_cm_belakang');
            $table->decimal('tinggi_cm_kiri', 5, 2)->nullable()->after('tinggi_cm_belakang');
            $table->decimal('lebar_cm_kanan', 5, 2)->nullable()->after('lebar_cm_kiri');
            $table->decimal('tinggi_cm_kanan', 5, 2)->nullable()->after('tinggi_cm_kiri');
        });

        // 4. Hapus tabel framework yang tidak digunakan
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore kolom kategori
        Schema::table('templates', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('file_template');
        });

        // Restore kolom tanggal_upload
        Schema::table('desains', function (Blueprint $table) {
            $table->date('tanggal_upload')->nullable()->after('detail_sablon');
        });

        // Hapus kolom kiri/kanan
        Schema::table('desains', function (Blueprint $table) {
            $table->dropColumn([
                'file_desain_kiri', 'file_desain_kanan',
                'lebar_cm_kiri', 'tinggi_cm_kiri',
                'lebar_cm_kanan', 'tinggi_cm_kanan'
            ]);
        });

        // Restore framework tables
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }
};
