<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_pengajuan', function (Blueprint $table) {
            $table->uuid('id_verifikasi')->primary();
            $table->uuid('id_pengajuan');
            $table->uuid('id_dosen');                       // Dosen PA
            $table->enum('keputusan', ['Disetujui', 'Ditolak', 'Revisi']);
            $table->text('catatan')->nullable();            // wajib utk Ditolak/Revisi (validasi di aplikasi)
            $table->timestamp('tanggal_verifikasi')->useCurrent();

            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_tempat')
                  ->restrictOnDelete();
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_pengajuan');
    }
};
