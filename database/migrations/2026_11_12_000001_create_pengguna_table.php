<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->uuid('id_pengguna')->primary();
            $table->string('email', 100)->unique();
            $table->string('password');                     // pengganti Supabase Auth
            $table->string('nama_lengkap', 100);
            $table->string('no_telepon', 20)->nullable();
            $table->enum('role', ['Mahasiswa', 'Dosen', 'Admin']);
            $table->boolean('status_aktif')->default(true);
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
