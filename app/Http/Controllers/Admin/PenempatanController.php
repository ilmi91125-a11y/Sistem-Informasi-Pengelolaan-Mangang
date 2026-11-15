<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\PengajuanTempat;
use App\Models\PenempatanMagang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenempatanController extends Controller
{
    public function index()
    {
        $daftar = PenempatanMagang::with(['mahasiswa.pengguna', 'tempat', 'dosenPembimbing.pengguna'])
            ->latest('created_at')->get();

        return view('admin.penempatan.index', compact('daftar'));
    }

    public function create()
    {
        return view('admin.penempatan.create', [
            'kandidat' => $this->kandidat(),
            'dosenList' => $this->dosenList(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pengajuan'        => ['required', 'string'],
            'tanggal_mulai'       => ['required', 'date'],
            'tanggal_selesai'     => ['required', 'date', 'after:tanggal_mulai'],
            'id_dosen_pembimbing' => ['nullable', 'string', 'exists:dosen,id_dosen'],
        ], [
            'id_pengajuan.required'    => 'Pilih pengajuan yang disetujui.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after'    => 'Tanggal selesai harus setelah tanggal mulai.',
            'id_dosen_pembimbing.exists' => 'Dosen pembimbing tidak valid.',
        ]);

        $pengajuan = $this->kandidat()->firstWhere('id_pengajuan', $data['id_pengajuan']);
        if (! $pengajuan) {
            return back()->withInput()->withErrors(['id_pengajuan' => 'Pengajuan tidak valid atau sudah memiliki penempatan.']);
        }

        if (! $pengajuan->tempat?->status_aktif) {
            return back()->withInput()->withErrors([
                'id_pengajuan' => 'Tempat magang ini belum aktif. Validasi/aktifkan dulu di menu Tempat Magang.',
            ]);
        }

        $mhs = $pengajuan->pendaftaran->mahasiswa;

        $penempatan = PenempatanMagang::create([
            'id_mahasiswa'        => $mhs->id_mahasiswa,
            'id_tempat'           => $pengajuan->id_tempat,
            'id_dosen_pembimbing' => $data['id_dosen_pembimbing'] ?? null,
            'tanggal_mulai'       => $data['tanggal_mulai'],
            'tanggal_selesai'     => $data['tanggal_selesai'],
            'status_magang'       => 'Berjalan',
        ]);

        $instansi = $pengajuan->tempat->nama_instansi;
        Notifikasi::create([
            'id_pengguna'      => $mhs->id_pengguna,
            'jenis_notifikasi' => 'Penempatan',
            'judul'            => 'Penempatan magang ditetapkan',
            'pesan'            => "Anda ditempatkan di {$instansi} pada {$data['tanggal_mulai']} s.d. {$data['tanggal_selesai']}.",
            'id_referensi'     => $penempatan->id_penempatan,
        ]);
        $this->kabarkanPembimbing($penempatan, $mhs, $instansi);

        return redirect()->route('admin.penempatan.index')->with('sukses', 'Penempatan berhasil dicatat.');
    }

    public function edit(string $id)
    {
        $p = PenempatanMagang::with(['mahasiswa.pengguna', 'tempat'])->findOrFail($id);

        return view('admin.penempatan.edit', ['p' => $p, 'dosenList' => $this->dosenList()]);
    }

    public function update(Request $request, string $id)
    {
        $p = PenempatanMagang::with(['mahasiswa.pengguna', 'tempat'])->findOrFail($id);

        $data = $request->validate([
            'tanggal_mulai'       => ['required', 'date'],
            'tanggal_selesai'     => ['required', 'date', 'after:tanggal_mulai'],
            'id_dosen_pembimbing' => ['nullable', 'string', 'exists:dosen,id_dosen'],
            'status_magang'       => ['required', Rule::in(['Berjalan', 'Selesai'])],
        ], [
            'tanggal_selesai.after'      => 'Tanggal selesai harus setelah tanggal mulai.',
            'id_dosen_pembimbing.exists' => 'Dosen pembimbing tidak valid.',
        ]);

        $dosenBaru = $data['id_dosen_pembimbing'] ?? null;
        $berubah = $dosenBaru && $dosenBaru !== $p->id_dosen_pembimbing;

        $p->update([
            'tanggal_mulai'       => $data['tanggal_mulai'],
            'tanggal_selesai'     => $data['tanggal_selesai'],
            'id_dosen_pembimbing' => $dosenBaru,
            'status_magang'       => $data['status_magang'],
        ]);

        if ($berubah) {
            $p->load('dosenPembimbing.pengguna');
            $this->kabarkanPembimbing($p, $p->mahasiswa, $p->tempat?->nama_instansi);

            Notifikasi::create([
                'id_pengguna'      => $p->mahasiswa->id_pengguna,
                'jenis_notifikasi' => 'Penempatan',
                'judul'            => 'Dosen pembimbing ditetapkan',
                'pesan'            => 'Dosen pembimbing magang Anda: ' . $p->dosenPembimbing?->pengguna?->nama_lengkap . '.',
                'id_referensi'     => $p->id_penempatan,
            ]);
        }

        return redirect()->route('admin.penempatan.index')->with('sukses', 'Data penempatan diperbarui.');
    }

    // ------------------------------------------------------------------

    /**
     * Pengajuan Disetujui yang belum punya penempatan
     * (dianggap sudah ditempatkan bila ada penempatan dengan mahasiswa + tempat yang sama).
     */
    private function kandidat()
    {
        $sudah = PenempatanMagang::get(['id_mahasiswa', 'id_tempat'])
            ->mapWithKeys(fn ($p) => [$p->id_mahasiswa . '|' . $p->id_tempat => true]);

        return PengajuanTempat::with(['pendaftaran.mahasiswa.pengguna', 'tempat'])
            ->where('status_pengajuan', 'Disetujui')
            ->latest('tanggal_pengajuan')->get()
            ->reject(fn ($p) => $sudah->has($p->pendaftaran->id_mahasiswa . '|' . $p->id_tempat))
            ->values();
    }

    private function dosenList()
    {
        return Dosen::with('pengguna')->get()
            ->sortBy(fn ($d) => $d->pengguna?->nama_lengkap)->values();
    }

    private function kabarkanPembimbing(PenempatanMagang $p, $mhs, ?string $instansi): void
    {
        $idPengguna = $p->dosenPembimbing?->id_pengguna
            ?? ($p->id_dosen_pembimbing ? Dosen::find($p->id_dosen_pembimbing)?->id_pengguna : null);
        if (! $idPengguna) {
            return;
        }

        Notifikasi::create([
            'id_pengguna'      => $idPengguna,
            'jenis_notifikasi' => 'Penempatan Baru',
            'judul'            => 'Anda menjadi dosen pembimbing',
            'pesan'            => 'Anda ditetapkan sebagai pembimbing magang ' . $mhs->pengguna->nama_lengkap
                                  . " ({$mhs->nim}) di {$instansi}.",
            'id_referensi'     => $p->id_penempatan,
        ]);
    }
}
