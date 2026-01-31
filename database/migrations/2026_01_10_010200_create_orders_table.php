<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pemesanan')->unique();
            $table->date('tanggal_pemesanan');

            $table->foreignId('reseller_id')->constrained('users');

            $table->unsignedBigInteger('total_estimasi')->default(0);

            $table->enum('status_pemesanan', ['MENUNGGU','DITERIMA','DITOLAK','DIPROSES','DIKIRIM','SELESAI'])
                ->default('MENUNGGU');

            $table->text('catatan_reseller')->nullable();
            $table->text('catatan_penjualan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
