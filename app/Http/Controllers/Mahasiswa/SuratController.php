<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Models\BerkasPengajuan;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\PengajuanTempat;
use App\Models\SuratMagang;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    use ResolvesMahasiswa;

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);

        $daftar = PengajuanTempat::with(['tempat', 'pendaftaran', 'surat'])
            ->where('status_pengajuan', 'Disetujui')
            ->whereHas('pendaftaran', fn ($q) => $q->where('id_mahasiswa', $mhs->id_mahasiswa))
            ->latest('tanggal_pengajuan')->get();

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('jenis_notifikasi', 'Status Surat')->update(['sudah_dibaca' => true]);

        return view('mahasiswa.surat.index', compact('daftar'));
    }

    /** Mahasiswa meminta surat untuk pengajuan yang sudah disetujui. */
    public function store(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $pengajuan = $this->milik($mhs->id_mahasiswa, $id);

        if ($pengajuan->status_pengajuan !== 'Disetujui') {
            return back()->with('galat', 'Surat hanya dapat diminta untuk pengajuan yang sudah disetujui.');
        }
        if ($pengajuan->surat) {
            return back()->with('galat', 'Permohonan surat untuk pengajuan ini sudah ada.');
        }

        $surat = SuratMagang::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_surat' => 'Diproses',
        ]);

        $this->kabarkanAdmin($surat->id_surat, 'Permohonan Surat', 'Permohonan surat magang baru',
            $request->user()->nama_lengkap . " ({$mhs->nim}) meminta surat magang untuk " . $pengajuan->tempat?->nama_instansi . '.');

        return redirect()->route('mahasiswa.surat.index')->with('sukses', 'Permohonan surat dikirim ke Tim Akademik.');
    }

    /** Melengkapi berkas saat surat berstatus "Perlu Dilengkapi". */
    public function lengkapi(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $pengajuan = $this->milik($mhs->id_mahasiswa, $id);
        $surat = $pengajuan->surat;

        if (! $surat || $surat->status_surat !== 'Perlu Dilengkapi') {
            return back()->with('galat', 'Surat ini tidak sedang meminta kelengkapan berkas.');
        }

        $request->validate([
            'berkas_tambahan'   => ['required', 'array', 'min:1'],
            'berkas_tambahan.*' => ['file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'berkas_tambahan.required' => 'Pilih minimal satu berkas untuk diunggah.',
            'berkas_tambahan.*.mimes'  => 'Berkas harus berformat PDF, DOC, atau DOCX.',
            'berkas_tambahan.*.max'    => 'Ukuran tiap berkas maksimal 5 MB.',
            'berkas_tambahan.*.uploaded' => 'Berkas gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ]);

        foreach ($request->file('berkas_tambahan') as $file) {
            BerkasPengajuan::create([
                'id_pengajuan'   => $pengajuan->id_pengajuan,
                'jenis_berkas'   => 'Kelengkapan Surat',
                'nama_berkas'    => $file->getClientOriginalName(),
                'url_berkas'     => $file->store('berkas_pengajuan', 'public'),
                'tanggal_unggah' => now(),
            ]);
        }

        $surat->update(['status_surat' => 'Diproses']);

        $this->kabarkanAdmin($surat->id_surat, 'Surat Dilengkapi', 'Berkas surat telah dilengkapi',
            $request->user()->nama_lengkap . " ({$mhs->nim}) telah melengkapi berkas untuk surat magang.");

        return redirect()->route('mahasiswa.surat.index')->with('sukses', 'Kelengkapan dikirim. Surat kembali diproses.');
    }

    // ------------------------------------------------------------------

    private function milik(string $idMahasiswa, string $id): PengajuanTempat
    {
        return PengajuanTempat::with(['tempat', 'surat'])
            ->where('id_pengajuan', $id)
            ->whereHas('pendaftaran', fn ($q) => $q->where('id_mahasiswa', $idMahasiswa))
            ->firstOrFail();
    }

    private function kabarkanAdmin(string $idSurat, string $jenis, string $judul, string $pesan): void
    {
        Pengguna::where('role', 'Admin')->where('status_aktif', true)->get()
            ->each(fn ($admin) => Notifikasi::create([
                'id_pengguna'      => $admin->id_pengguna,
                'jenis_notifikasi' => $jenis,
                'judul'            => $judul,
                'pesan'            => $pesan,
                'id_referensi'     => $idSurat,
            ]));
    }
}
