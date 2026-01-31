<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pembayaran')->unique();
            $table->date('tanggal_input');

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('reseller_id')->constrained('users');

            $table->unsignedBigInteger('total_penjualan')->default(0);

            $table->enum('metode_pembayaran', ['TRANSFER','TUNAI'])->nullable();
            $table->string('bukti_transfer')->nullable();

            $table->enum('status_validasi', ['MENUNGGU','BUTUH_REVISI','VALID','DITOLAK'])->default('MENUNGGU');
            $table->text('catatan_validasi')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
