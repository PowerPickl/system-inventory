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
        Schema::create('barang_backup', function (Blueprint $table) {
            $table->unsignedBigInteger('id_barang')->default(0);
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('kode_barang');
            $table->string('kode_internal', 20)->nullable();
            $table->integer('sequence_number')->default(1);
            $table->string('nama_barang');
            $table->string('merk', 100)->nullable();
            $table->string('model_tipe', 100)->nullable();
            $table->string('satuan');
            $table->decimal('harga_beli', 10);
            $table->decimal('harga_jual', 10);
            $table->integer('reorder_point');
            $table->decimal('annual_demand', 10)->nullable();
            $table->decimal('ordering_cost', 10)->default(5000);
            $table->decimal('holding_cost', 10)->nullable();
            $table->decimal('demand_avg_daily')->nullable();
            $table->decimal('demand_max_daily')->nullable();
            $table->integer('eoq_qty')->nullable();
            $table->integer('eoq_calculated')->nullable();
            $table->integer('safety_stock')->nullable();
            $table->integer('rop_calculated')->nullable();
            $table->timestamp('last_eoq_calculation')->nullable();
            $table->text('eoq_notes')->nullable();
            $table->integer('lead_time')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('keterangan_detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_backup');
    }
};
