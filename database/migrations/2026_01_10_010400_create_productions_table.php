<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_batch')->unique();
            $table->date('tanggal_produksi');

            $table->foreignId('product_id')->constrained('products');

            $table->foreignId('pjpu_id')->constrained('users');
            $table->foreignId('produksi_input_by')->nullable()->constrained('users');

            $table->integer('target_produksi');
            $table->integer('hasil_berhasil')->default(0);
            $table->integer('hasil_gagal')->default(0);

            $table->enum('status_produksi', ['RENCANA','PROSES','SELESAI'])->default('RENCANA');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
