<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add parent_id to desains
        Schema::table('desains', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id_desain');
            $table->foreign('parent_id')->references('id_desain')->on('desains')->onDelete('set null');
        });

        // orders table changes
        DB::statement("ALTER TABLE orders ADD COLUMN payment_status ENUM('unpaid', 'awaiting_payment', 'awaiting_verification', 'paid', 'failed') NOT NULL DEFAULT 'unpaid' AFTER status_order");
        
        // Update status_order enum
        // Map existing data first to prevent truncation errors by temporarily converting to VARCHAR
        DB::statement("ALTER TABLE orders MODIFY COLUMN status_order VARCHAR(50) NOT NULL");
        DB::statement("UPDATE orders SET status_order = 'reviewing' WHERE status_order = 'pending'");
        DB::statement("UPDATE orders SET status_order = 'processing' WHERE status_order = 'diproses'");
        DB::statement("UPDATE orders SET status_order = 'completed' WHERE status_order = 'selesai'");
        DB::statement("UPDATE orders SET status_order = 'cancelled' WHERE status_order = 'dibatalkan'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN status_order ENUM('reviewing', 'pending_payment', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'reviewing'");

        // order_details table changes
        DB::statement("ALTER TABLE order_details MODIFY COLUMN status_desain VARCHAR(50) NOT NULL");
        DB::statement("UPDATE order_details SET status_desain = 'revision_required' WHERE status_desain = 'revisi'");
        DB::statement("UPDATE order_details SET status_desain = 'approved' WHERE status_desain = 'disetujui'");
        DB::statement("ALTER TABLE order_details MODIFY COLUMN status_desain ENUM('pending', 'revision_required', 'approved') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desains', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });

        DB::statement("ALTER TABLE orders DROP COLUMN payment_status");
        
        DB::statement("ALTER TABLE orders MODIFY COLUMN status_order VARCHAR(50) NOT NULL");
        DB::statement("UPDATE orders SET status_order = 'pending' WHERE status_order = 'reviewing' OR status_order = 'pending_payment'");
        DB::statement("UPDATE orders SET status_order = 'diproses' WHERE status_order = 'processing'");
        DB::statement("UPDATE orders SET status_order = 'selesai' WHERE status_order = 'completed'");
        DB::statement("UPDATE orders SET status_order = 'dibatalkan' WHERE status_order = 'cancelled'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN status_order ENUM('pending', 'diproses', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending'");

        DB::statement("ALTER TABLE order_details MODIFY COLUMN status_desain VARCHAR(50) NOT NULL");
        DB::statement("UPDATE order_details SET status_desain = 'revisi' WHERE status_desain = 'revision_required'");
        DB::statement("UPDATE order_details SET status_desain = 'disetujui' WHERE status_desain = 'approved'");
        DB::statement("ALTER TABLE order_details MODIFY COLUMN status_desain ENUM('disetujui', 'revisi', 'pending') NOT NULL DEFAULT 'pending'");
    }
};
