<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Concerns\ResolvesDosen;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\LaporanMagang;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    use ResolvesDosen;

    private const STATUS = ['Diunggah', 'Revisi', 'Diperiksa'];

    public function index(Request $request)
    {
        $dosen = $this->dosen($request);

        $status = $request->query('status', 'Diunggah');
        if ($status !== 'semua' && ! in_array($status, self::STATUS, true)) {
            $status = 'Diunggah';
        }

        $dasar = fn () => LaporanMagang::whereHas('penempatan',
            fn ($q) => $q->where('id_dosen_pembimbing', $dosen->id_dosen));

        $jumlah = $dasar()->selectRaw('status_laporan, count(*) as jml')
            ->groupBy('status_laporan')->pluck('jml', 'status_laporan');

        $daftar = $dasar()->with(['penempatan.mahasiswa.pengguna', 'penempatan.tempat'])
            ->when($status !== 'semua', fn ($q) => $q->where('status_laporan', $status))
            ->latest('tanggal_unggah')->take(100)->get();

        return view('dosen.laporan.index', [
            'daftar' => $daftar, 'status' => $status, 'jumlah' => $jumlah, 'semuaStatus' => self::STATUS,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $laporan = $this->milik($dosen, $id);

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('id_referensi', $laporan->id_laporan)->update(['sudah_dibaca' => true]);

        return view('dosen.laporan.show', compact('laporan'));
    }

    public function periksa(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $laporan = $this->milik($dosen, $id);

        if ($laporan->status_laporan !== 'Diunggah') {
            return redirect()->route('dosen.laporan.show', $id)
                ->with('galat', 'Laporan ini sudah diperiksa sebelumnya.');
        }

        $data = $request->validate([
            'keputusan'     => ['required', Rule::in(['Diperiksa', 'Revisi'])],
            'catatan_dosen' => [Rule::requiredIf($request->input('keputusan') === 'Revisi'),
                                'nullable', 'string', 'max:3000'],
        ], [
            'keputusan.required'     => 'Pilih keputusan terlebih dahulu.',
            'keputusan.in'           => 'Keputusan tidak valid.',
            'catatan_dosen.required' => 'Catatan wajib diisi untuk permintaan revisi.',
            'catatan_dosen.max'      => 'Catatan maksimal 3000 karakter.',
        ]);

        $laporan->update([
            'status_laporan' => $data['keputusan'],
            'catatan_dosen'  => $data['catatan_dosen'] ?? null,
        ]);

        $pesan = $data['keputusan'] === 'Diperiksa'
            ? "Laporan \"{$laporan->judul_laporan}\" telah diperiksa dan diterima."
            : "Laporan \"{$laporan->judul_laporan}\" perlu direvisi. Catatan: {$data['catatan_dosen']}";

        Notifikasi::create([
            'id_pengguna'      => $laporan->penempatan->mahasiswa->id_pengguna,
            'jenis_notifikasi' => 'Hasil Laporan',
            'judul'            => 'Laporan ' . ($data['keputusan'] === 'Diperiksa' ? 'diterima' : 'perlu revisi'),
            'pesan'            => $pesan,
            'id_referensi'     => $laporan->id_laporan,
        ]);

        return redirect()->route('dosen.laporan.show', $id)
            ->with('sukses', 'Hasil pemeriksaan tersimpan dan mahasiswa telah diberi tahu.');
    }

    private function milik(Dosen $dosen, string $id): LaporanMagang
    {
        return LaporanMagang::with(['penempatan.mahasiswa.pengguna', 'penempatan.tempat'])
            ->where('id_laporan', $id)
            ->whereHas('penempatan', fn ($q) => $q->where('id_dosen_pembimbing', $dosen->id_dosen))
            ->firstOrFail();
    }
}
