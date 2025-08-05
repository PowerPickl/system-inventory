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
        Schema::create('barang', function (Blueprint $table) {
            $table->bigIncrements('id_barang');
            $table->unsignedBigInteger('id_kategori')->nullable()->index('barang_id_kategori_foreign');
            $table->string('kode_barang_old', 50)->nullable()->unique('barang_kode_barang_unique');
            $table->string('kode_barang', 50)->nullable()->unique('barang_kode_internal_unique');
            $table->integer('sequence_number')->default(1);
            $table->string('nama_barang');
            $table->string('merk', 100)->nullable();
            $table->string('model_tipe', 100)->nullable();
            $table->string('satuan');
            $table->decimal('harga_beli', 10);
            $table->decimal('harga_jual', 10);
            $table->integer('reorder_point');
            $table->decimal('annual_demand', 10)->nullable();
            $table->decimal('ordering_cost', 10)->nullable()->default(5000);
            $table->decimal('holding_cost', 10)->nullable();
            $table->decimal('demand_avg_daily')->nullable();
            $table->decimal('demand_max_daily')->nullable();
            $table->integer('eoq_qty')->nullable();
            $table->integer('eoq_calculated')->nullable();
            $table->integer('safety_stock')->nullable();
            $table->integer('rop_calculated')->nullable();
            $table->timestamp('last_eoq_calculation')->nullable()->index('idx_last_calculation');
            $table->text('eoq_notes')->nullable();
            $table->integer('lead_time')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('keterangan_detail')->nullable();
            $table->timestamps();

            $table->index(['annual_demand', 'ordering_cost', 'holding_cost'], 'idx_eoq_params');
            $table->index(['eoq_calculated', 'rop_calculated'], 'idx_eoq_results');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
