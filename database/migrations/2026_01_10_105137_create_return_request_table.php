<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_retur')->unique();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('reseller_id')->constrained('users')->cascadeOnDelete();

            $table->date('tanggal_pengajuan')->nullable();
            $table->string('bukti_foto')->nullable(); // path storage
            $table->text('catatan_reseller')->nullable();

            $table->enum('status_validasi', ['MENUNGGU','VALID','BUTUH_REVISI','DITOLAK'])->default('MENUNGGU');
            $table->text('catatan_validasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
