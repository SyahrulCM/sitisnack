<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();

            $table->date('tanggal_jadwal_kirim')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_diterima')->nullable();

            $table->text('alamat_kirim')->nullable();
            $table->string('kurir_driver')->nullable();

            $table->enum('status_pengiriman', ['MENUNGGU','DIJADWALKAN','DIKIRIM','DITERIMA'])
                ->default('MENUNGGU');

            $table->string('bukti_kirim')->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
