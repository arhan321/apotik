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
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_id')->constrained('jenis')->onDelete('cascade');
            $table->foreignId('golongan_id')->constrained('golongans')->onDelete('cascade');
            $table->string('kode_obat');
            $table->string('nama_obat');
            $table->text('komposisi');
            $table->string('dosis');
            $table->string('aturan_pakai');
            $table->string('nomor_izin_edaar')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->decimal('harga', 10, 2);
            $table->integer('stok');
            $table->string('image')->nullable();
            $table->enum('status_label', ['Dengan_Resep', 'Tanpa_Resep']);
            $table->enum('status', ['Tersedia', 'Kosong', 'Kadaluarsa']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
