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
        Schema::create('restock_request_detail', function (Blueprint $table) {
            $table->bigIncrements('id_request_detail');
            $table->unsignedBigInteger('id_request');
            $table->unsignedBigInteger('id_barang')->index('restock_request_detail_id_barang_foreign');
            $table->integer('qty_request');
            $table->integer('qty_approved')->nullable();
            $table->decimal('estimasi_harga', 12)->nullable();
            $table->text('alasan_request')->nullable();
            $table->timestamps();

            $table->index(['id_request', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_request_detail');
    }
};
