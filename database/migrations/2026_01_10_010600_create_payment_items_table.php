<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');

            $table->integer('qty_terjual')->default(0);
            $table->integer('qty_sisa')->default(0);

            $table->unsignedBigInteger('harga');    // snapshot
            $table->unsignedBigInteger('subtotal'); // qty_terjual * harga

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
