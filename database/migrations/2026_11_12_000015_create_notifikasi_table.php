<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->uuid('id_notifikasi')->primary();
            $table->uuid('id_pengguna');                    // penerima
            $table->string('jenis_notifikasi', 30);
            $table->string('judul', 100);
            $table->text('pesan');
            $table->uuid('id_referensi')->nullable();       // rujukan logis, sengaja bukan FK
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')
                  ->restrictOnDelete();
            $table->index(['id_pengguna', 'sudah_dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
