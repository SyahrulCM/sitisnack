<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('kode_retur')->unique();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('reseller_id')->constrained('users')->cascadeOnDelete();

            $table->date('tanggal_pengajuan');
            $table->string('bukti_foto')->nullable();

            // ✅ TAMBAHKAN INI
            $table->text('catatan_reseller')->nullable();
            $table->text('catatan_penjualan')->nullable();

            $table->enum('status_validasi', ['MENUNGGU','VALID','BUTUH_REVISI','DITOLAK'])
                ->default('MENUNGGU');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
