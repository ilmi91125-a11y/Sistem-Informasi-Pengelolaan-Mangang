<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->uuid('id_dosen')->primary();
            $table->uuid('id_pengguna')->unique();
            $table->string('nidn', 20)->unique();
            $table->string('bidang_keahlian', 100)->nullable();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
