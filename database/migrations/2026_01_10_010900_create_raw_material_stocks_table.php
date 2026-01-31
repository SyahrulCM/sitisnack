<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_material_stocks', function (Blueprint $table) {
            $table->id();

            $table->string('nama_bahan');
            $table->string('satuan')->default('kg');

            $table->decimal('stok_akhir', 12, 2)->default(0);
            $table->decimal('stok_minimum', 12, 2)->default(0);

            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_stocks');
    }
};
