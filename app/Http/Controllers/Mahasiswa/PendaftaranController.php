<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use ResolvesMahasiswa;

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);

        $daftar = PendaftaranMagang::where('id_mahasiswa', $mhs->id_mahasiswa)
            ->latest('tanggal_daftar')->get();

        return view('mahasiswa.pendaftaran', compact('mhs', 'daftar'));
    }

    public function store(Request $request)
    {
        $mhs = $this->mhs($request);

        $data = $request->validate([
            'periode_magang' => ['required', 'string', 'max:30'],
        ], [
            'periode_magang.required' => 'Periode magang wajib diisi.',
            'periode_magang.max'      => 'Periode magang maksimal 30 karakter.',
        ]);

        $periode = trim($data['periode_magang']);

        // 1 mahasiswa hanya boleh mendaftar 1x per periode
        $sudahAda = PendaftaranMagang::where('id_mahasiswa', $mhs->id_mahasiswa)
            ->where('periode_magang', $periode)->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->withErrors(['periode_magang' => 'Anda sudah mendaftar pada periode tersebut.']);
        }

        PendaftaranMagang::create([
            'id_mahasiswa'       => $mhs->id_mahasiswa,
            'periode_magang'     => $periode,
            'tanggal_daftar'     => now()->toDateString(),
            'status_pendaftaran' => 'Terdaftar',
        ]);

        return redirect()->route('mahasiswa.pendaftaran')
            ->with('sukses', 'Pendaftaran magang berhasil. Silakan lanjut mengajukan tempat magang.');
    }
}
