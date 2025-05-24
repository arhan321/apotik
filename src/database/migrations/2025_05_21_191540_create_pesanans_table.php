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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('nomor_pesanan');
            $table->date('tanggal')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->foreignId('pengajuan_id')->nullable()->constrained('pengajuans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
