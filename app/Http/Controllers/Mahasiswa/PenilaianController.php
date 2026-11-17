<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dosen\PenilaianController as DosenPenilaian;
use App\Models\Notifikasi;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    use ResolvesMahasiswa;

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);

        $penempatan = PenempatanMagang::with(['tempat', 'dosenPembimbing.pengguna', 'penilaian'])
            ->where('id_mahasiswa', $mhs->id_mahasiswa)
            ->latest('created_at')->first();

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('jenis_notifikasi', 'Nilai Magang')->update(['sudah_dibaca' => true]);

        return view('mahasiswa.penilaian.index', [
            'penempatan' => $penempatan,
            'nilai' => $penempatan?->penilaian,
            'bobotDosen' => (int) (DosenPenilaian::BOBOT_DOSEN * 100),
            'bobotInstansi' => (int) (DosenPenilaian::BOBOT_INSTANSI * 100),
        ]);
    }
}
