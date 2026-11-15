<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Concerns\ResolvesDosen;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\PengajuanTempat;
use App\Models\VerifikasiPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerifikasiController extends Controller
{
    use ResolvesDosen;

    private const STATUS = ['Diajukan', 'Revisi', 'Disetujui', 'Ditolak'];

    public function index(Request $request)
    {
        $dosen = $this->dosen($request);

        $status = $request->query('status', 'Diajukan');
        if ($status !== 'semua' && ! in_array($status, self::STATUS, true)) {
            $status = 'Diajukan';
        }

        // Hanya pengajuan milik mahasiswa yang dosen ini menjadi PA-nya
        $dasar = fn () => PengajuanTempat::whereHas(
            'pendaftaran.mahasiswa', fn ($q) => $q->where('id_dosen_pa', $dosen->id_dosen)
        );

        $jumlah = $dasar()->selectRaw('status_pengajuan, count(*) as jml')
            ->groupBy('status_pengajuan')->pluck('jml', 'status_pengajuan');

        $daftar = $dasar()->with(['pendaftaran.mahasiswa.pengguna', 'tempat'])
            ->when($status !== 'semua', fn ($q) => $q->where('status_pengajuan', $status))
            ->latest('tanggal_pengajuan')->take(100)->get();

        return view('dosen.verifikasi.index', [
            'daftar' => $daftar,
            'status' => $status,
            'jumlah' => $jumlah,
            'semuaStatus' => self::STATUS,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $pengajuan = $this->milik($dosen, $id, ['tempat', 'berkas', 'pendaftaran.mahasiswa.pengguna']);

        $riwayat = $pengajuan->verifikasi()->with('dosen.pengguna')
            ->latest('tanggal_verifikasi')->get();

        // Tandai notifikasi terkait pengajuan ini sudah dibaca
        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('id_referensi', $pengajuan->id_pengajuan)
            ->update(['sudah_dibaca' => true]);

        return view('dosen.verifikasi.show', compact('pengajuan', 'riwayat'));
    }

    public function store(Request $request, string $id)
    {
        $dosen = $this->dosen($request);
        $pengajuan = $this->milik($dosen, $id, ['tempat', 'pendaftaran.mahasiswa.pengguna']);

        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['Disetujui', 'Ditolak', 'Revisi'])],
            'catatan'   => [Rule::requiredIf($request->input('keputusan') !== 'Disetujui'),
                            'nullable', 'string', 'max:2000'],
        ], [
            'keputusan.required' => 'Pilih keputusan terlebih dahulu.',
            'keputusan.in'       => 'Keputusan tidak valid.',
            'catatan.required'   => 'Catatan wajib diisi untuk keputusan Ditolak atau Revisi.',
            'catatan.max'        => 'Catatan maksimal 2000 karakter.',
        ]);

        $berhasil = DB::transaction(function () use ($pengajuan, $dosen, $data) {
            // Kunci baris agar tidak diputuskan dua kali bersamaan
            $terkini = PengajuanTempat::where('id_pengajuan', $pengajuan->id_pengajuan)
                ->lockForUpdate()->first();

            if (! $terkini || $terkini->status_pengajuan !== 'Diajukan') {
                return false;
            }

            VerifikasiPengajuan::create([
                'id_pengajuan'       => $terkini->id_pengajuan,
                'id_dosen'           => $dosen->id_dosen,
                'keputusan'          => $data['keputusan'],
                'catatan'            => $data['catatan'] ?? null,
                'tanggal_verifikasi' => now(),
            ]);

            $terkini->update(['status_pengajuan' => $data['keputusan']]);

            $this->kabarkan($pengajuan, $data['keputusan'], $data['catatan'] ?? null);

            return true;
        });

        if (! $berhasil) {
            return redirect()->route('dosen.verifikasi.show', $id)
                ->with('galat', 'Pengajuan ini sudah diputuskan sebelumnya.');
        }

        return redirect()->route('dosen.verifikasi.show', $id)
            ->with('sukses', 'Keputusan "' . $data['keputusan'] . '" berhasil disimpan dan mahasiswa telah diberi tahu.');
    }

    // ------------------------------------------------------------------

    /** Ambil pengajuan hanya jika mahasiswanya dibimbing dosen ini sebagai PA (selain itu 404). */
    private function milik(Dosen $dosen, string $id, array $with = []): PengajuanTempat
    {
        return PengajuanTempat::with($with)
            ->where('id_pengajuan', $id)
            ->whereHas('pendaftaran.mahasiswa', fn ($q) => $q->where('id_dosen_pa', $dosen->id_dosen))
            ->firstOrFail();
    }

    private function kabarkan(PengajuanTempat $pengajuan, string $keputusan, ?string $catatan): void
    {
        $mhs = $pengajuan->pendaftaran->mahasiswa;
        $instansi = $pengajuan->tempat?->nama_instansi;

        $kalimat = match ($keputusan) {
            'Disetujui' => 'disetujui oleh dosen PA',
            'Ditolak'   => 'ditolak oleh dosen PA',
            default     => 'perlu direvisi',
        };

        // Ke mahasiswa
        Notifikasi::create([
            'id_pengguna'      => $mhs->id_pengguna,
            'jenis_notifikasi' => 'Hasil Verifikasi',
            'judul'            => 'Pengajuan ' . $keputusan,
            'pesan'            => "Pengajuan tempat magang Anda di {$instansi} {$kalimat}."
                                  . ($catatan ? " Catatan: {$catatan}" : ''),
            'id_referensi'     => $pengajuan->id_pengajuan,
        ]);

        // Ke Tim Akademik, agar surat magang dapat diproses
        if ($keputusan === 'Disetujui') {
            Pengguna::where('role', 'Admin')->where('status_aktif', true)->get()
                ->each(fn ($admin) => Notifikasi::create([
                    'id_pengguna'      => $admin->id_pengguna,
                    'jenis_notifikasi' => 'Pengajuan Disetujui',
                    'judul'            => 'Pengajuan disetujui dosen PA',
                    'pesan'            => $mhs->pengguna->nama_lengkap . " ({$mhs->nim}) - {$instansi}. Surat magang dapat diproses.",
                    'id_referensi'     => $pengajuan->id_pengajuan,
                ]));
        }
    }
}
