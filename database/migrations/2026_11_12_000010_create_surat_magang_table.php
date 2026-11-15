<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_magang', function (Blueprint $table) {
            $table->uuid('id_surat')->primary();
            $table->uuid('id_pengajuan')->unique();         // 1 pengajuan = maks 1 surat
            $table->uuid('id_pemroses')->nullable();        // Admin pemroses
            $table->string('nomor_surat', 50)->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->enum('status_surat', ['Diproses', 'Perlu Dilengkapi', 'Selesai'])
                  ->default('Diproses');
            $table->text('catatan')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('url_berkas')->nullable();

            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_tempat')
                  ->restrictOnDelete();
            $table->foreign('id_pemroses')->references('id_pengguna')->on('pengguna')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_magang');
    }
};
