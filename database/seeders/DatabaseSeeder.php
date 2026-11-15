<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\KetentuanMagang;
use App\Models\Mahasiswa;
use App\Models\Pengguna;
use App\Models\TempatMagang;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Password semua akun contoh: "password"  (ganti sebelum produksi!)
        $admin = Pengguna::create([
            'email' => 'admin@magang.test', 'password' => 'password',
            'nama_lengkap' => 'Tim Akademik', 'role' => 'Admin',
        ]);

        $uDosen = Pengguna::create([
            'email' => 'dosen@magang.test', 'password' => 'password',
            'nama_lengkap' => 'Haditya Prasetyo, S.Kom, M.Kom.', 'role' => 'Dosen',
        ]);
        $dosen = Dosen::create([
            'id_pengguna' => $uDosen->id_pengguna,
            'nidn' => '0000000001', 'bidang_keahlian' => 'Sistem Informasi',
        ]);

        $uMhs = Pengguna::create([
            'email' => 'mahasiswa@magang.test', 'password' => 'password',
            'nama_lengkap' => 'Muhammad Wafa Arifin', 'role' => 'Mahasiswa',
        ]);
        Mahasiswa::create([
            'id_pengguna' => $uMhs->id_pengguna, 'id_dosen_pa' => $dosen->id_dosen,
            'nim' => '2417020027', 'program_studi' => 'Sistem Informasi', 'angkatan' => 2024,
        ]);

        TempatMagang::create([
            'nama_instansi' => 'PT Contoh Teknologi',
            'alamat' => 'Jl. Contoh No. 1, Padang',
            'bidang_usaha' => 'Teknologi Informasi',
            'kontak' => 'hrd@contoh.test',
            'deskripsi' => 'Magang pengembangan aplikasi web.',
            'sumber_data' => 'Admin',
        ]);

        KetentuanMagang::create([
            'id_pengguna' => $admin->id_pengguna,
            'judul' => 'Ketentuan Umum Magang',
            'isi_ketentuan' => 'Mahasiswa mencari tempat magang secara mandiri dan mengajukannya melalui sistem untuk diverifikasi dosen PA.',
        ]);
    }
}
