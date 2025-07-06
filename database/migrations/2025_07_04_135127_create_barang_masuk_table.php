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
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id('id_masuk');
            $table->string('nomor_masuk')->unique(); // BM001, BM002, etc
            $table->unsignedBigInteger('id_request')->nullable(); // jika dari restock request
            $table->datetime('tanggal_masuk');
            $table->unsignedBigInteger('id_user_gudang'); // user gudang yang input
            $table->string('supplier')->nullable();
            $table->string('nomor_invoice')->nullable();
            $table->decimal('total_nilai', 15, 2)->nullable();
            $table->enum('jenis_masuk', ['Restock Request', 'Pembelian Manual', 'Return', 'Adjustment'])->default('Restock Request');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_request')->references('id_request')->on('restock_request')->onDelete('set null');
            $table->foreign('id_user_gudang')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('nomor_masuk');
            $table->index('tanggal_masuk');
            $table->index('jenis_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};