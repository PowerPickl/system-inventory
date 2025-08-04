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
        // Update enum to add 'Ordered' status
        DB::statement("ALTER TABLE restock_request MODIFY COLUMN status_request ENUM('Pending', 'Approved', 'Ordered', 'Completed', 'Rejected', 'Terminated') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back (remove 'Ordered')
        DB::statement("ALTER TABLE restock_request MODIFY COLUMN status_request ENUM('Pending', 'Approved', 'Completed', 'Rejected', 'Terminated') DEFAULT 'Pending'");
    }
};