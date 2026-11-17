<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        [$daftar, $f] = $this->ambil($request);

        $ringkasan = [
            'total'    => $daftar->count(),
            'berjalan' => $daftar->where('status_magang', 'Berjalan')->count(),
            'selesai'  => $daftar->where('status_magang', 'Selesai')->count(),
            'dinilai'  => $daftar->filter(fn ($p) => $p->penilaian?->nilai_akhir !== null)->count(),
        ];

        $dosenList = Dosen::with('pengguna')->get()
            ->sortBy(fn ($d) => $d->pengguna?->nama_lengkap)->values();

        return view('admin.rekap.index', compact('daftar', 'f', 'ringkasan', 'dosenList'));
    }

    /** Unduh CSV (pemisah titik koma + BOM UTF-8 agar langsung rapi di Excel Indonesia). */
    public function ekspor(Request $request)
    {
        [$daftar] = $this->ambil($request);
        $nama = 'rekapitulasi-magang-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($daftar) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['NIM', 'Nama', 'Program Studi', 'Angkatan', 'Tempat Magang', 'Mulai', 'Selesai',
                           'Dosen Pembimbing', 'Status Magang', 'Jumlah Bimbingan', 'Status Laporan',
                           'Nilai Dosen', 'Nilai Instansi', 'Nilai Akhir'], ';');

            foreach ($daftar as $p) {
                $m = $p->mahasiswa;
                $n = $p->penilaian;
                fputcsv($out, [
                    $m?->nim, $m?->pengguna?->nama_lengkap, $m?->program_studi, $m?->angkatan,
                    $p->tempat?->nama_instansi, $p->tanggal_mulai?->format('d/m/Y'), $p->tanggal_selesai?->format('d/m/Y'),
                    $p->dosenPembimbing?->pengguna?->nama_lengkap, $p->status_magang, $p->bimbingan_count,
                    $p->laporan->sortByDesc('tanggal_unggah')->first()?->status_laporan,
                    $n?->nilai_dosen, $n?->nilai_instansi, $n?->nilai_akhir,
                ], ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: \Illuminate\Support\Collection, 1: array} */
    private function ambil(Request $request): array
    {
        $f = [
            'status' => $request->query('status', 'semua'),
            'dosen'  => $request->query('dosen', ''),
            'q'      => trim((string) $request->query('q', '')),
        ];
        if (! in_array($f['status'], ['semua', 'Berjalan', 'Selesai'], true)) {
            $f['status'] = 'semua';
        }

        $daftar = PenempatanMagang::with([
                'mahasiswa.pengguna', 'tempat', 'dosenPembimbing.pengguna', 'penilaian', 'laporan',
            ])
            ->withCount('bimbingan')
            ->when($f['status'] !== 'semua', fn ($q) => $q->where('status_magang', $f['status']))
            ->when($f['dosen'] !== '', fn ($q) => $q->where('id_dosen_pembimbing', $f['dosen']))
            ->when($f['q'] !== '', fn ($q) => $q->whereHas('mahasiswa', fn ($m) => $m->where(fn ($g) => $g
                ->where('nim', 'like', "%{$f['q']}%")
                ->orWhereHas('pengguna', fn ($u) => $u->where('nama_lengkap', 'like', "%{$f['q']}%")))))
            ->latest('created_at')->get();

        return [$daftar, $f];
    }
}
