<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_tempat', function (Blueprint $table) {
            $table->uuid('id_pengajuan')->primary();
            $table->uuid('id_pendaftaran');
            $table->uuid('id_tempat');
            $table->text('rencana_kegiatan')->nullable();
            $table->date('rencana_mulai')->nullable();
            $table->date('rencana_selesai')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->enum('status_pengajuan', ['Diajukan', 'Revisi', 'Disetujui', 'Ditolak'])
                  ->default('Diajukan');

            $table->foreign('id_pendaftaran')->references('id_pendaftaran')->on('pendaftaran_magang')
                  ->restrictOnDelete();
            $table->foreign('id_tempat')->references('id_tempat')->on('tempat_magang')
                  ->restrictOnDelete();
            $table->index('status_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_tempat');
    }
};
