<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_magang', function (Blueprint $table) {
            $table->uuid('id_pendaftaran')->primary();
            $table->uuid('id_mahasiswa');
            $table->string('periode_magang', 30);
            $table->date('tanggal_daftar');
            $table->enum('status_pendaftaran', ['Terdaftar', 'Dibatalkan'])->default('Terdaftar');

            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')
                  ->restrictOnDelete();
            // 1 mahasiswa hanya boleh mendaftar 1x per periode
            $table->unique(['id_mahasiswa', 'periode_magang'], 'uq_pendaftaran_mhs_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_magang');
    }
};
