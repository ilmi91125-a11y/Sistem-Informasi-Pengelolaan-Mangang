<?php

use App\Http\Controllers\Admin\DataMahasiswaController;
use App\Http\Controllers\Admin\KetentuanController;
use App\Http\Controllers\Admin\PenempatanController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\SuratController as AdminSuratController;
use App\Http\Controllers\Admin\TempatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\BimbinganController as DosenBimbinganController;
use App\Http\Controllers\Dosen\LaporanController as DosenLaporanController;
use App\Http\Controllers\Dosen\PenilaianController as DosenPenilaianController;
use App\Http\Controllers\Dosen\VerifikasiController;
use App\Http\Controllers\Mahasiswa\BimbinganController;
use App\Http\Controllers\Mahasiswa\InformasiController;
use App\Http\Controllers\Mahasiswa\LaporanController;
use App\Http\Controllers\Mahasiswa\PenilaianController;
use App\Http\Controllers\Mahasiswa\PendaftaranController;
use App\Http\Controllers\Mahasiswa\PengajuanController;
use App\Http\Controllers\Mahasiswa\SuratController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Login & logout
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Area setelah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])
        ->middleware(RoleMiddleware::class . ':Dosen')->name('dashboard.dosen');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware(RoleMiddleware::class . ':Admin')->name('dashboard.admin');

    // ---------------- ADMIN ----------------
    Route::middleware(RoleMiddleware::class . ':Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/rekapitulasi', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekapitulasi/ekspor', [RekapController::class, 'ekspor'])->name('rekap.ekspor');

        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('/pengguna/baru', [PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('/pengguna/{id}/ubah', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::patch('/pengguna/{id}/status', [PenggunaController::class, 'status'])->name('pengguna.status');

        Route::get('/mahasiswa', [DataMahasiswaController::class, 'index'])->name('mahasiswa.index');

        Route::get('/ketentuan', [KetentuanController::class, 'index'])->name('ketentuan.index');
        Route::get('/ketentuan/baru', [KetentuanController::class, 'create'])->name('ketentuan.create');
        Route::post('/ketentuan', [KetentuanController::class, 'store'])->name('ketentuan.store');
        Route::get('/ketentuan/{id}/ubah', [KetentuanController::class, 'edit'])->name('ketentuan.edit');
        Route::put('/ketentuan/{id}', [KetentuanController::class, 'update'])->name('ketentuan.update');
        Route::delete('/ketentuan/{id}', [KetentuanController::class, 'destroy'])->name('ketentuan.destroy');

        Route::get('/tempat', [TempatController::class, 'index'])->name('tempat.index');
        Route::get('/tempat/baru', [TempatController::class, 'create'])->name('tempat.create');
        Route::post('/tempat', [TempatController::class, 'store'])->name('tempat.store');
        Route::get('/tempat/{id}/ubah', [TempatController::class, 'edit'])->name('tempat.edit');
        Route::put('/tempat/{id}', [TempatController::class, 'update'])->name('tempat.update');
        Route::patch('/tempat/{id}/status', [TempatController::class, 'status'])->name('tempat.status');

        Route::get('/surat', [AdminSuratController::class, 'index'])->name('surat.index');
        Route::get('/surat/{id}', [AdminSuratController::class, 'show'])->name('surat.show');
        Route::put('/surat/{id}', [AdminSuratController::class, 'update'])->name('surat.update');

        Route::get('/penempatan', [PenempatanController::class, 'index'])->name('penempatan.index');
        Route::get('/penempatan/baru', [PenempatanController::class, 'create'])->name('penempatan.create');
        Route::post('/penempatan', [PenempatanController::class, 'store'])->name('penempatan.store');
        Route::get('/penempatan/{id}/ubah', [PenempatanController::class, 'edit'])->name('penempatan.edit');
        Route::put('/penempatan/{id}', [PenempatanController::class, 'update'])->name('penempatan.update');
    });

    // ---------------- DOSEN ----------------
    Route::middleware(RoleMiddleware::class . ':Dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/penilaian', [DosenPenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/{id}/nilai', [DosenPenilaianController::class, 'edit'])->name('penilaian.edit');
        Route::put('/penilaian/{id}', [DosenPenilaianController::class, 'simpan'])->name('penilaian.simpan');

        Route::get('/laporan', [DosenLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/{id}', [DosenLaporanController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{id}', [DosenLaporanController::class, 'periksa'])->name('laporan.periksa');

        Route::get('/bimbingan', [DosenBimbinganController::class, 'index'])->name('bimbingan.index');
        Route::get('/bimbingan/{id}', [DosenBimbinganController::class, 'show'])->name('bimbingan.show');
        Route::post('/bimbingan/{id}', [DosenBimbinganController::class, 'tanggapi'])->name('bimbingan.tanggapi');

        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{id}', [VerifikasiController::class, 'store'])->name('verifikasi.store');
    });

    // ---------------- MAHASISWA ----------------
    Route::middleware(RoleMiddleware::class . ':Mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard');

        Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');

        Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran');
        Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

        Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
        Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

        Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
        Route::post('/bimbingan', [BimbinganController::class, 'store'])->name('bimbingan.store');
        Route::get('/bimbingan/{id}/ubah', [BimbinganController::class, 'edit'])->name('bimbingan.edit');
        Route::put('/bimbingan/{id}', [BimbinganController::class, 'update'])->name('bimbingan.update');
        Route::delete('/bimbingan/{id}', [BimbinganController::class, 'destroy'])->name('bimbingan.destroy');

        Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
        Route::post('/surat/{id}', [SuratController::class, 'store'])->name('surat.store');
        Route::post('/surat/{id}/lengkapi', [SuratController::class, 'lengkapi'])->name('surat.lengkapi');

        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/pengajuan/{id}/revisi', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])->name('pengajuan.update');
    });
});
