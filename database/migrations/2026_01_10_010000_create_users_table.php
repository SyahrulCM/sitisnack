<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('nama_lengkap');
            $table->string('username')->unique();

            // email tetap ada biar kompatibel dengan auth bawaan (boleh nullable)
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->enum('role', ['admin','pjpu','produksi','penjualan','distribusi','reseller'])
                ->default('reseller');

            $table->string('no_hp', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('status_aktif')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
