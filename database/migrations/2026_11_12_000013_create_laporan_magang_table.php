<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_magang', function (Blueprint $table) {
            $table->uuid('id_laporan')->primary();
            $table->uuid('id_penempatan');
            $table->string('judul_laporan', 200);
            $table->text('url_berkas');
            $table->timestamp('tanggal_unggah')->useCurrent();
            $table->enum('status_laporan', ['Diunggah', 'Diperiksa', 'Revisi'])->default('Diunggah');
            $table->text('catatan_dosen')->nullable();

            $table->foreign('id_penempatan')->references('id_penempatan')->on('penempatan_magang')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_magang');
    }
};
