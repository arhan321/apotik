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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('nomor_pengajuan');
            $table->text('catatan')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('alamat');
            $table->integer('jarak');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['menunggu', 'diproses', 'disetujui', 'ditolak'])->default('menunggu');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
