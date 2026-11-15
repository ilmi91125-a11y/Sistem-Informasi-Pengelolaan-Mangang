<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tempat_magang', function (Blueprint $table) {
            $table->uuid('id_tempat')->primary();
            $table->string('nama_instansi', 150);
            $table->text('alamat');
            $table->string('bidang_usaha', 100)->nullable();
            $table->string('kontak', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('sumber_data', ['Admin', 'Mahasiswa'])->default('Admin');
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_magang');
    }
};
