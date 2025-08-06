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
            $table->bigIncrements('id_masuk');
            $table->string('nomor_masuk')->index();
            $table->unsignedBigInteger('id_request')->nullable()->index('barang_masuk_id_request_foreign');
            $table->dateTime('tanggal_masuk')->index();
            $table->unsignedBigInteger('id_user_gudang')->index('barang_masuk_id_user_gudang_foreign');
            $table->string('supplier')->nullable();
            $table->string('nomor_invoice')->nullable();
            $table->decimal('total_nilai', 15)->nullable();
            $table->enum('jenis_masuk', ['Restock Request', 'Pembelian Manual', 'Return', 'Adjustment'])->default('Restock Request')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['nomor_masuk']);
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
