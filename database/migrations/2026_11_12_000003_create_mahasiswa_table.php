<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->uuid('id_mahasiswa')->primary();
            $table->uuid('id_pengguna')->unique();
            $table->uuid('id_dosen_pa');                    // Dosen Penasihat Akademik
            $table->string('nim', 15)->unique();
            $table->string('program_studi', 50);
            $table->smallInteger('angkatan');

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')
                  ->restrictOnDelete();
            $table->foreign('id_dosen_pa')->references('id_dosen')->on('dosen')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
