<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->uuid('id_penilaian')->primary();
            $table->uuid('id_penempatan')->unique();        // 1 penempatan = 1 penilaian
            $table->decimal('nilai_dosen', 5, 2)->nullable();
            $table->decimal('nilai_instansi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_input')->useCurrent();

            $table->foreign('id_penempatan')->references('id_penempatan')->on('penempatan_magang')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
