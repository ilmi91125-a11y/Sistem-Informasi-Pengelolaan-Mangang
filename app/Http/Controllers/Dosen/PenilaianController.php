<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Concerns\ResolvesDosen;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\Penilaian;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    use ResolvesDosen;

    /**
     * Bobot nilai akhir. Ubah angka di sini bila ketentuan program studi berbeda
     * (jumlah kedua bobot harus 1).
     */
    public const BOBOT_DOSEN = 0.5;
    public const BOBOT_INSTANSI = 0.5;

    public function index(Request $request)
    {
        $dosen = $this->dosen($request);

        $daftar = PenempatanMagang::with(['mahasiswa.pengguna', 'tempat', 'penilaian', 'laporan'])
            ->where('id_dosen_pembimbing', $dosen->id_dosen)
            ->latest('created_at')->get();

        return view('dosen.penilaian.index', compact('daftar'));
    }

    public function edit(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $p = $this->milik($dosen, $id);

        $laporanDiterima = $p->laporan->contains('status_laporan', 'Diperiksa');

        return view('dosen.penilaian.form', [
            'p' => $p,
            'nilai' => $p->penilaian,
            'laporanDiterima' => $laporanDiterima,
            'bobotDosen' => (int) (self::BOBOT_DOSEN * 100),
            'bobotInstansi' => (int) (self::BOBOT_INSTANSI * 100),
        ]);
    }

    public function simpan(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $p = $this->milik($dosen, $id);

        $data = $request->validate([
            'nilai_dosen'    => ['required', 'numeric', 'between:0,100'],
            'nilai_instansi' => ['required', 'numeric', 'between:0,100'],
            'catatan'        => ['nullable', 'string', 'max:2000'],
        ], [
            'nilai_dosen.required'    => 'Nilai dosen wajib diisi.',
            'nilai_dosen.numeric'     => 'Nilai dosen harus berupa angka.',
            'nilai_dosen.between'     => 'Nilai harus antara 0 sampai 100.',
            'nilai_instansi.required' => 'Nilai instansi wajib diisi.',
            'nilai_instansi.numeric'  => 'Nilai instansi harus berupa angka.',
            'nilai_instansi.between'  => 'Nilai harus antara 0 sampai 100.',
            'catatan.max'             => 'Catatan maksimal 2000 karakter.',
        ]);

        $akhir = round(
            $data['nilai_dosen'] * self::BOBOT_DOSEN + $data['nilai_instansi'] * self::BOBOT_INSTANSI, 2
        );

        $sudahAda = Penilaian::where('id_penempatan', $p->id_penempatan)->exists();

        Penilaian::updateOrCreate(
            ['id_penempatan' => $p->id_penempatan],
            [
                'nilai_dosen'    => $data['nilai_dosen'],
                'nilai_instansi' => $data['nilai_instansi'],
                'nilai_akhir'    => $akhir,
                'catatan'        => $data['catatan'] ?? null,
                'tanggal_input'  => now(),
            ]
        );

        Notifikasi::create([
            'id_pengguna'      => $p->mahasiswa->id_pengguna,
            'jenis_notifikasi' => 'Nilai Magang',
            'judul'            => $sudahAda ? 'Nilai magang diperbarui' : 'Nilai magang tersedia',
            'pesan'            => 'Nilai akhir magang Anda: ' . number_format($akhir, 2, ',', '.') . '.',
            'id_referensi'     => $p->id_penempatan,
        ]);

        return redirect()->route('dosen.penilaian.index')
            ->with('sukses', 'Nilai tersimpan. Nilai akhir: ' . number_format($akhir, 2, ',', '.') . '.');
    }

    private function milik(Dosen $dosen, string $id): PenempatanMagang
    {
        return PenempatanMagang::with(['mahasiswa.pengguna', 'tempat', 'penilaian', 'laporan'])
            ->where('id_penempatan', $id)
            ->where('id_dosen_pembimbing', $dosen->id_dosen)
            ->firstOrFail();
    }
}
