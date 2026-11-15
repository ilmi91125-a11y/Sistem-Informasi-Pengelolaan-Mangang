<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan', function (Blueprint $table) {
            $table->uuid('id_bimbingan')->primary();
            $table->uuid('id_penempatan');
            $table->date('tanggal_bimbingan');
            $table->string('topik', 150);
            $table->text('uraian_kegiatan')->nullable();
            $table->text('tanggapan_dosen')->nullable();
            $table->timestamp('tanggal_tanggapan')->nullable();
            $table->enum('status_bimbingan', ['Menunggu', 'Ditanggapi'])->default('Menunggu');

            $table->foreign('id_penempatan')->references('id_penempatan')->on('penempatan_magang')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan');
    }
};
