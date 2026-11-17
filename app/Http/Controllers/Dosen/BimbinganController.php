<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Concerns\ResolvesDosen;
use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class BimbinganController extends Controller
{
    use ResolvesDosen;

    private const STATUS = ['Menunggu', 'Ditanggapi'];

    public function index(Request $request)
    {
        $dosen = $this->dosen($request);

        $status = $request->query('status', 'Menunggu');
        if ($status !== 'semua' && ! in_array($status, self::STATUS, true)) {
            $status = 'Menunggu';
        }

        $dasar = fn () => Bimbingan::whereHas('penempatan',
            fn ($q) => $q->where('id_dosen_pembimbing', $dosen->id_dosen));

        $jumlah = $dasar()->selectRaw('status_bimbingan, count(*) as jml')
            ->groupBy('status_bimbingan')->pluck('jml', 'status_bimbingan');

        $daftar = $dasar()->with(['penempatan.mahasiswa.pengguna', 'penempatan.tempat'])
            ->when($status !== 'semua', fn ($q) => $q->where('status_bimbingan', $status))
            ->orderByDesc('tanggal_bimbingan')->take(100)->get();

        return view('dosen.bimbingan.index', [
            'daftar' => $daftar, 'status' => $status, 'jumlah' => $jumlah, 'semuaStatus' => self::STATUS,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $b = $this->milik($dosen, $id);

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('id_referensi', $b->id_bimbingan)->update(['sudah_dibaca' => true]);

        return view('dosen.bimbingan.show', compact('b'));
    }

    public function tanggapi(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $b = $this->milik($dosen, $id);

        $data = $request->validate([
            'tanggapan_dosen' => ['required', 'string', 'max:3000'],
        ], [
            'tanggapan_dosen.required' => 'Tanggapan wajib diisi.',
            'tanggapan_dosen.max'      => 'Tanggapan maksimal 3000 karakter.',
        ]);

        $b->update([
            'tanggapan_dosen'   => $data['tanggapan_dosen'],
            'tanggal_tanggapan' => now(),
            'status_bimbingan'  => 'Ditanggapi',
        ]);

        Notifikasi::create([
            'id_pengguna'      => $b->penempatan->mahasiswa->id_pengguna,
            'jenis_notifikasi' => 'Tanggapan Bimbingan',
            'judul'            => 'Bimbingan Anda ditanggapi',
            'pesan'            => "Dosen pembimbing menanggapi bimbingan \"{$b->topik}\".",
            'id_referensi'     => $b->id_bimbingan,
        ]);

        return redirect()->route('dosen.bimbingan.show', $id)
            ->with('sukses', 'Tanggapan tersimpan dan mahasiswa telah diberi tahu.');
    }

    /** Hanya bimbingan milik mahasiswa yang dosen ini menjadi pembimbingnya (selain itu 404). */
    private function milik(Dosen $dosen, string $id): Bimbingan
    {
        return Bimbingan::with(['penempatan.mahasiswa.pengguna', 'penempatan.tempat'])
            ->where('id_bimbingan', $id)
            ->whereHas('penempatan', fn ($q) => $q->where('id_dosen_pembimbing', $dosen->id_dosen))
            ->firstOrFail();
    }
}
