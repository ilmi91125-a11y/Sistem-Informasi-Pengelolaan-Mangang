<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Models\LaporanMagang;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    use ResolvesMahasiswa;

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);
        $penempatan = $this->penempatan($mhs);

        $daftar = $penempatan
            ? LaporanMagang::where('id_penempatan', $penempatan->id_penempatan)
                ->latest('tanggal_unggah')->get()
            : collect();

        // Boleh unggah bila belum ada laporan yang menunggu pemeriksaan / sudah diterima
        $bolehUnggah = $penempatan && ! $daftar->whereIn('status_laporan', ['Diunggah', 'Diperiksa'])->count();

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('jenis_notifikasi', 'Hasil Laporan')->update(['sudah_dibaca' => true]);

        return view('mahasiswa.laporan.index', compact('penempatan', 'daftar', 'bolehUnggah'));
    }

    public function store(Request $request)
    {
        $mhs = $this->mhs($request);
        $penempatan = $this->penempatan($mhs);

        if (! $penempatan) {
            return back()->with('galat', 'Anda belum memiliki penempatan magang.');
        }

        $tertahan = LaporanMagang::where('id_penempatan', $penempatan->id_penempatan)
            ->whereIn('status_laporan', ['Diunggah', 'Diperiksa'])->exists();
        if ($tertahan) {
            return back()->with('galat', 'Laporan Anda sedang diperiksa atau sudah diterima, sehingga tidak dapat mengunggah lagi.');
        }

        $data = $request->validate([
            'judul_laporan' => ['required', 'string', 'max:200'],
            'berkas'        => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ], [
            'judul_laporan.required' => 'Judul laporan wajib diisi.',
            'judul_laporan.max'      => 'Judul laporan maksimal 200 karakter.',
            'berkas.required'        => 'Pilih berkas laporan.',
            'berkas.mimes'           => 'Berkas harus berformat PDF, DOC, atau DOCX.',
            'berkas.max'             => 'Ukuran berkas maksimal 10 MB.',
            'berkas.uploaded'        => 'Berkas gagal diunggah. Pastikan ukurannya tidak lebih dari 10 MB.',
        ]);

        $laporan = LaporanMagang::create([
            'id_penempatan'  => $penempatan->id_penempatan,
            'judul_laporan'  => $data['judul_laporan'],
            'url_berkas'     => $request->file('berkas')->store('laporan_magang', 'public'),
            'tanggal_unggah' => now(),
            'status_laporan' => 'Diunggah',
        ]);

        $dosenId = $penempatan->dosenPembimbing?->id_pengguna;
        if ($dosenId) {
            Notifikasi::create([
                'id_pengguna'      => $dosenId,
                'jenis_notifikasi' => 'Laporan Baru',
                'judul'            => 'Laporan magang diunggah',
                'pesan'            => $request->user()->nama_lengkap . " ({$mhs->nim}) mengunggah laporan: {$data['judul_laporan']}.",
                'id_referensi'     => $laporan->id_laporan,
            ]);
        }

        return redirect()->route('mahasiswa.laporan.index')
            ->with('sukses', 'Laporan berhasil diunggah.' . ($dosenId ? '' : ' Dosen pembimbing belum ditetapkan, pemeriksaan menunggu penetapan.'));
    }

    /** Hapus laporan yang belum diperiksa (untuk mengunggah ulang berkas yang salah). */
    public function destroy(Request $request, string $id)
    {
        $mhs = $this->mhs($request);

        $laporan = LaporanMagang::where('id_laporan', $id)
            ->whereHas('penempatan', fn ($q) => $q->where('id_mahasiswa', $mhs->id_mahasiswa))
            ->firstOrFail();

        if ($laporan->status_laporan !== 'Diunggah') {
            return back()->with('galat', 'Laporan yang sudah diperiksa tidak dapat dihapus.');
        }

        Storage::disk('public')->delete($laporan->url_berkas);
        $laporan->delete();

        return redirect()->route('mahasiswa.laporan.index')->with('sukses', 'Laporan dihapus. Anda dapat mengunggah ulang.');
    }

    private function penempatan(Mahasiswa $mhs): ?PenempatanMagang
    {
        return PenempatanMagang::with(['tempat', 'dosenPembimbing.pengguna'])
            ->where('id_mahasiswa', $mhs->id_mahasiswa)
            ->latest('created_at')->first();
    }
}
