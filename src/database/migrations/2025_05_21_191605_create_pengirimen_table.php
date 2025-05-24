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
        Schema::create('pengirimen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans');
            $table->foreignId('pengirim_id')->nullable()->constrained('pengirims');
            $table->string('alamat');
            $table->decimal('jarak', 8, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->enum('status', ['menunggu','dikirim', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengirimen');
    }
};
