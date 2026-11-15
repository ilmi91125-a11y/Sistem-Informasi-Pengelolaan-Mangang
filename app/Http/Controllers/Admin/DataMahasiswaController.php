<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class DataMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $q  = trim((string) $request->query('q', ''));
        $pa = $request->query('dosen');

        $daftar = Mahasiswa::with(['pengguna', 'dosenPa.pengguna', 'penempatan'])
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w
                ->where('nim', 'like', "%{$q}%")
                ->orWhereHas('pengguna', fn ($p) => $p->where('nama_lengkap', 'like', "%{$q}%"))))
            ->when($pa, fn ($x) => $x->where('id_dosen_pa', $pa))
            ->orderBy('nim')->take(300)->get();

        $dosenList = Dosen::with('pengguna')->get()
            ->sortBy(fn ($d) => $d->pengguna?->nama_lengkap)->values();

        return view('admin.mahasiswa.index', compact('daftar', 'dosenList', 'q', 'pa'));
    }
}
