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
        Schema::create('stock_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama/kode report
            $table->date('report_date'); // tanggal laporan
            $table->integer('total_items')->default(0); // jumlah jenis produk
            $table->bigInteger('total_cost_value')->default(0); // total nilai harga modal
            $table->bigInteger('total_selling_value')->default(0); // total nilai harga jual
            $table->string('path_file')->nullable(); // path file excel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reports');
    }
};
