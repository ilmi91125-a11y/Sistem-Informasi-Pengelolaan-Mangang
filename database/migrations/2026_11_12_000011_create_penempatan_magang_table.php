<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penempatan_magang', function (Blueprint $table) {
            $table->uuid('id_penempatan')->primary();
            $table->uuid('id_mahasiswa');
            $table->uuid('id_tempat');
            $table->uuid('id_dosen_pembimbing')->nullable(); // ditetapkan Tim Akademik setelah penempatan
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_magang', ['Berjalan', 'Selesai'])->default('Berjalan');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')
                  ->restrictOnDelete();
            $table->foreign('id_tempat')->references('id_tempat')->on('tempat_magang')
                  ->restrictOnDelete();
            $table->foreign('id_dosen_pembimbing')->references('id_dosen')->on('dosen')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_magang');
    }
};
