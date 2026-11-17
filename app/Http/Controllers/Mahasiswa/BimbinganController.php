<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;

class BimbinganController extends Controller
{
    use ResolvesMahasiswa;

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);
        $penempatan = $this->penempatan($mhs);

        $daftar = $penempatan
            ? Bimbingan::where('id_penempatan', $penempatan->id_penempatan)
                ->orderByDesc('tanggal_bimbingan')->get()
            : collect();

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('jenis_notifikasi', 'Tanggapan Bimbingan')->update(['sudah_dibaca' => true]);

        return view('mahasiswa.bimbingan.index', compact('penempatan', 'daftar'));
    }

    public function store(Request $request)
    {
        $mhs = $this->mhs($request);
        $penempatan = $this->penempatan($mhs);

        if (! $penempatan || $penempatan->status_magang !== 'Berjalan') {
            return back()->with('galat', 'Bimbingan hanya dapat diisi saat magang Anda berstatus Berjalan.');
        }

        $data = $this->validasi($request, $penempatan);

        $bimbingan = Bimbingan::create([
            'id_penempatan'     => $penempatan->id_penempatan,
            'tanggal_bimbingan' => $data['tanggal_bimbingan'],
            'topik'             => $data['topik'],
            'uraian_kegiatan'   => $data['uraian_kegiatan'],
            'status_bimbingan'  => 'Menunggu',
        ]);

        $dosenId = $penempatan->dosenPembimbing?->id_pengguna;
        if ($dosenId) {
            Notifikasi::create([
                'id_pengguna'      => $dosenId,
                'jenis_notifikasi' => 'Bimbingan Baru',
                'judul'            => 'Catatan bimbingan baru',
                'pesan'            => $request->user()->nama_lengkap . " ({$mhs->nim}) mengisi bimbingan: {$data['topik']}.",
                'id_referensi'     => $bimbingan->id_bimbingan,
            ]);
        }

        return redirect()->route('mahasiswa.bimbingan.index')
            ->with('sukses', 'Catatan bimbingan tersimpan.' . ($dosenId ? '' : ' Dosen pembimbing belum ditetapkan, tanggapan menunggu penetapan.'));
    }

    public function edit(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $b = $this->milik($mhs, $id);

        if ($b->status_bimbingan !== 'Menunggu') {
            return redirect()->route('mahasiswa.bimbingan.index')
                ->with('galat', 'Bimbingan yang sudah ditanggapi tidak dapat diubah.');
        }

        return view('mahasiswa.bimbingan.edit', ['b' => $b]);
    }

    public function update(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $b = $this->milik($mhs, $id);

        if ($b->status_bimbingan !== 'Menunggu') {
            return redirect()->route('mahasiswa.bimbingan.index')
                ->with('galat', 'Bimbingan yang sudah ditanggapi tidak dapat diubah.');
        }

        $data = $this->validasi($request, $b->penempatan);
        $b->update($data);

        return redirect()->route('mahasiswa.bimbingan.index')->with('sukses', 'Catatan bimbingan diperbarui.');
    }

    public function destroy(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $b = $this->milik($mhs, $id);

        if ($b->status_bimbingan !== 'Menunggu') {
            return redirect()->route('mahasiswa.bimbingan.index')
                ->with('galat', 'Bimbingan yang sudah ditanggapi tidak dapat dihapus.');
        }

        $b->delete();

        return redirect()->route('mahasiswa.bimbingan.index')->with('sukses', 'Catatan bimbingan dihapus.');
    }

    // ------------------------------------------------------------------

    /** Penempatan terbaru milik mahasiswa. */
    private function penempatan(Mahasiswa $mhs): ?PenempatanMagang
    {
        return PenempatanMagang::with(['tempat', 'dosenPembimbing.pengguna'])
            ->where('id_mahasiswa', $mhs->id_mahasiswa)
            ->latest('created_at')->first();
    }

    private function milik(Mahasiswa $mhs, string $id): Bimbingan
    {
        return Bimbingan::with('penempatan')
            ->where('id_bimbingan', $id)
            ->whereHas('penempatan', fn ($q) => $q->where('id_mahasiswa', $mhs->id_mahasiswa))
            ->firstOrFail();
    }

    private function validasi(Request $request, PenempatanMagang $penempatan): array
    {
        $mulai = $penempatan->tanggal_mulai->toDateString();

        return $request->validate([
            'tanggal_bimbingan' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:' . $mulai],
            'topik'             => ['required', 'string', 'max:150'],
            'uraian_kegiatan'   => ['required', 'string', 'max:5000'],
        ], [
            'tanggal_bimbingan.required'        => 'Tanggal bimbingan wajib diisi.',
            'tanggal_bimbingan.before_or_equal' => 'Tanggal bimbingan tidak boleh di masa depan.',
            'tanggal_bimbingan.after_or_equal'  => 'Tanggal bimbingan tidak boleh sebelum magang dimulai (' . $penempatan->tanggal_mulai->format('d/m/Y') . ').',
            'topik.required'                    => 'Topik wajib diisi.',
            'topik.max'                         => 'Topik maksimal 150 karakter.',
            'uraian_kegiatan.required'          => 'Uraian kegiatan wajib diisi.',
            'uraian_kegiatan.max'               => 'Uraian maksimal 5000 karakter.',
        ]);
    }
}
