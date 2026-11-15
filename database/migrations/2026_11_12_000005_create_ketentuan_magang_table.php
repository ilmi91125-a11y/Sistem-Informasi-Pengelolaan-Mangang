<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketentuan_magang', function (Blueprint $table) {
            $table->uuid('id_ketentuan')->primary();
            $table->uuid('id_pengguna');                    // Admin pembuat/pengubah
            $table->string('judul', 150);
            $table->text('isi_ketentuan');
            $table->text('url_berkas')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketentuan_magang');
    }
};
