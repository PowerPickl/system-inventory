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
        // Add new columns to restock_request table
        Schema::table('restock_request', function (Blueprint $table) {
            // Add termination fields
            $table->timestamp('tanggal_terminated')->nullable()->after('tanggal_approved');
            $table->unsignedBigInteger('id_user_terminated')->nullable()->after('tanggal_terminated');
            
            // Add ordered tracking fields  
            $table->timestamp('tanggal_ordered')->nullable()->after('id_user_terminated');
            $table->unsignedBigInteger('id_user_ordered')->nullable()->after('tanggal_ordered');
            
            // Foreign key constraints
            $table->foreign('id_user_terminated')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_user_ordered')->references('id')->on('users')->onDelete('set null');
        });

        // Update enum status - add 'Terminated' option
        DB::statement("ALTER TABLE restock_request MODIFY COLUMN status_request ENUM('Pending', 'Approved', 'Rejected', 'Completed', 'Terminated') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restock_request', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['id_user_terminated']);
            $table->dropForeign(['id_user_ordered']);
            
            // Drop columns
            $table->dropColumn([
                'tanggal_terminated', 
                'id_user_terminated', 
                'tanggal_ordered', 
                'id_user_ordered'
            ]);
        });

        // Revert enum status back to original
        DB::statement("ALTER TABLE restock_request MODIFY COLUMN status_request ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending'");
    }
};