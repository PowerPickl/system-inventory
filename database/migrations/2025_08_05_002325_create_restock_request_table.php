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
            $table->bigIncrements('id_request');
            $table->string('nomor_request')->index();
            $table->unsignedBigInteger('id_user_gudang')->index('restock_request_id_user_gudang_foreign');
            $table->dateTime('tanggal_request')->index();
            $table->enum('status_request', ['Pending', 'Approved', 'Ordered', 'Completed', 'Rejected', 'Terminated', 'Cancelled'])->nullable()->default('Pending')->index();
            $table->unsignedBigInteger('id_user_approved')->nullable()->index('restock_request_id_user_approved_foreign');
            $table->dateTime('tanggal_approved')->nullable();
            $table->timestamp('tanggal_terminated')->nullable();
            $table->unsignedBigInteger('id_user_terminated')->nullable()->index('restock_request_id_user_terminated_foreign');
            $table->timestamp('tanggal_ordered')->nullable();
            $table->unsignedBigInteger('id_user_ordered')->nullable()->index('restock_request_id_user_ordered_foreign');
            $table->text('catatan_request')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();

            $table->unique(['nomor_request']);
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
