<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_pengajuan', function (Blueprint $table) {
            $table->uuid('id_berkas')->primary();
            $table->uuid('id_pengajuan');
            $table->string('jenis_berkas', 50);             // proposal, surat penerimaan, dst.
            $table->string('nama_berkas', 150);
            $table->text('url_berkas');                     // path di storage Laravel
            $table->timestamp('tanggal_unggah')->useCurrent();

            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_tempat')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_pengajuan');
    }
};
