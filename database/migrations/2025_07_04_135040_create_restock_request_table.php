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
        Schema::create('restock_request', function (Blueprint $table) {
            $table->id('id_request');
            $table->string('nomor_request')->unique(); // REQ001, REQ002, etc
            $table->unsignedBigInteger('id_user_gudang'); // user gudang yang request
            $table->datetime('tanggal_request');
            $table->enum('status_request', ['Pending', 'Approved', 'Rejected', 'Completed'])->default('Pending');
            $table->unsignedBigInteger('id_user_approved')->nullable(); // owner yang approve
            $table->datetime('tanggal_approved')->nullable();
            $table->text('catatan_request')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user_gudang')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_user_approved')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('nomor_request');
            $table->index('status_request');
            $table->index('tanggal_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_request');
    }
};