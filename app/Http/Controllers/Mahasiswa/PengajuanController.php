<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Concerns\ResolvesMahasiswa;
use App\Http\Controllers\Controller;
use App\Models\BerkasPengajuan;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\PendaftaranMagang;
use App\Models\PengajuanTempat;
use App\Models\TempatMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    use ResolvesMahasiswa;

    /** Status pengajuan yang dianggap masih "aktif" untuk satu pendaftaran. */
    private const STATUS_AKTIF = ['Diajukan', 'Revisi', 'Disetujui'];

    private const JENIS_BERKAS = [
        'proposal'         => 'Proposal',
        'surat_penerimaan' => 'Surat Penerimaan',
        'berkas_lain'      => 'Berkas Lainnya',
    ];

    public function index(Request $request)
    {
        $mhs = $this->mhs($request);

        $daftar = PengajuanTempat::with(['tempat', 'pendaftaran'])
            ->whereHas('pendaftaran', fn ($q) => $q->where('id_mahasiswa', $mhs->id_mahasiswa))
            ->latest('tanggal_pengajuan')->get();

        return view('mahasiswa.pengajuan.index', compact('daftar'));
    }

    public function create(Request $request)
    {
        $mhs = $this->mhs($request);

        $pendaftaranTersedia = PendaftaranMagang::where('id_mahasiswa', $mhs->id_mahasiswa)
            ->where('status_pendaftaran', 'Terdaftar')
            ->whereDoesntHave('pengajuan', fn ($q) => $q->whereIn('status_pengajuan', self::STATUS_AKTIF))
            ->latest('tanggal_daftar')->get();

        $tempatList = TempatMagang::where('status_aktif', true)->orderBy('nama_instansi')->get();

        return view('mahasiswa.pengajuan.create', compact('pendaftaranTersedia', 'tempatList'));
    }

    public function store(Request $request)
    {
        $mhs = $this->mhs($request);
        $baru = $request->input('tempat_mode') === 'baru';
        $pilih = $request->input('tempat_mode') === 'pilih';

        $data = $request->validate([
            'id_pendaftaran'   => ['required', 'string'],
            'tempat_mode'      => ['required', Rule::in(['pilih', 'baru'])],
            'id_tempat'        => [Rule::requiredIf($pilih), 'nullable', 'string'],
            'nama_instansi'    => [Rule::requiredIf($baru), 'nullable', 'string', 'max:150'],
            'alamat'           => [Rule::requiredIf($baru), 'nullable', 'string'],
            'bidang_usaha'     => ['nullable', 'string', 'max:100'],
            'kontak'           => ['nullable', 'string', 'max:100'],
            'rencana_kegiatan' => ['required', 'string'],
            'rencana_mulai'    => ['required', 'date'],
            'rencana_selesai'  => ['required', 'date', 'after:rencana_mulai'],
            'proposal'         => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'surat_penerimaan' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'berkas_lain'      => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], $this->pesan());

        // Pendaftaran harus milik mahasiswa ini dan masih berstatus Terdaftar
        $pendaftaran = PendaftaranMagang::where('id_pendaftaran', $data['id_pendaftaran'])
            ->where('id_mahasiswa', $mhs->id_mahasiswa)
            ->where('status_pendaftaran', 'Terdaftar')->first();

        if (! $pendaftaran) {
            return back()->withInput()->withErrors(['id_pendaftaran' => 'Pendaftaran tidak valid.']);
        }

        $adaAktif = PengajuanTempat::where('id_pendaftaran', $pendaftaran->id_pendaftaran)
            ->whereIn('status_pengajuan', self::STATUS_AKTIF)->exists();

        if ($adaAktif) {
            return back()->withInput()->withErrors([
                'id_pendaftaran' => 'Pendaftaran ini sudah memiliki pengajuan yang masih aktif.',
            ]);
        }

        if ($pilih) {
            $tempat = TempatMagang::where('id_tempat', $data['id_tempat'])
                ->where('status_aktif', true)->first();
            if (! $tempat) {
                return back()->withInput()->withErrors(['id_tempat' => 'Tempat magang tidak valid.']);
            }
        }

        $pengajuan = DB::transaction(function () use ($data, $pilih, $pendaftaran, $request) {
            if ($pilih) {
                $idTempat = $data['id_tempat'];
            } else {
                // Tempat usulan mahasiswa: nonaktif sampai divalidasi Tim Akademik
                $idTempat = TempatMagang::create([
                    'nama_instansi' => $data['nama_instansi'],
                    'alamat'        => $data['alamat'],
                    'bidang_usaha'  => $data['bidang_usaha'] ?? null,
                    'kontak'        => $data['kontak'] ?? null,
                    'sumber_data'   => 'Mahasiswa',
                    'status_aktif'  => false,
                ])->id_tempat;
            }

            $p = PengajuanTempat::create([
                'id_pendaftaran'   => $pendaftaran->id_pendaftaran,
                'id_tempat'        => $idTempat,
                'rencana_kegiatan' => $data['rencana_kegiatan'],
                'rencana_mulai'    => $data['rencana_mulai'],
                'rencana_selesai'  => $data['rencana_selesai'],
                'status_pengajuan' => 'Diajukan',
            ]);

            $this->simpanBerkas($p, $request);

            return $p;
        });

        $this->kabarkanDosen($mhs, $pengajuan, 'Pengajuan Baru', 'Pengajuan tempat magang baru');

        return redirect()->route('mahasiswa.pengajuan.show', $pengajuan->id_pengajuan)
            ->with('sukses', 'Pengajuan berhasil dikirim dan menunggu verifikasi dosen PA.');
    }

    public function show(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $pengajuan = $this->milik($mhs, $id, ['tempat', 'pendaftaran', 'berkas', 'surat']);

        $riwayat = $pengajuan->verifikasi()->with('dosen.pengguna')
            ->latest('tanggal_verifikasi')->get();

        // Tandai notifikasi terkait pengajuan ini sudah dibaca
        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('id_referensi', $pengajuan->id_pengajuan)
            ->update(['sudah_dibaca' => true]);

        return view('mahasiswa.pengajuan.show', compact('pengajuan', 'riwayat'));
    }

    public function edit(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $pengajuan = $this->milik($mhs, $id, ['tempat', 'berkas']);

        if ($pengajuan->status_pengajuan !== 'Revisi') {
            return redirect()->route('mahasiswa.pengajuan.show', $id)
                ->with('galat', 'Pengajuan hanya dapat diperbaiki saat berstatus Revisi.');
        }

        $catatan = $pengajuan->verifikasi()->where('keputusan', 'Revisi')
            ->latest('tanggal_verifikasi')->first();

        return view('mahasiswa.pengajuan.edit', compact('pengajuan', 'catatan'));
    }

    public function update(Request $request, string $id)
    {
        $mhs = $this->mhs($request);
        $pengajuan = $this->milik($mhs, $id);

        if ($pengajuan->status_pengajuan !== 'Revisi') {
            return redirect()->route('mahasiswa.pengajuan.show', $id)
                ->with('galat', 'Pengajuan hanya dapat diperbaiki saat berstatus Revisi.');
        }

        $data = $request->validate([
            'rencana_kegiatan' => ['required', 'string'],
            'rencana_mulai'    => ['required', 'date'],
            'rencana_selesai'  => ['required', 'date', 'after:rencana_mulai'],
            'proposal'         => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'surat_penerimaan' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'berkas_lain'      => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], $this->pesan());

        DB::transaction(function () use ($pengajuan, $data, $request) {
            $pengajuan->update([
                'rencana_kegiatan' => $data['rencana_kegiatan'],
                'rencana_mulai'    => $data['rencana_mulai'],
                'rencana_selesai'  => $data['rencana_selesai'],
                'status_pengajuan' => 'Diajukan',
            ]);
            $this->simpanBerkas($pengajuan, $request);
        });

        $this->kabarkanDosen($mhs, $pengajuan, 'Pengajuan Direvisi', 'Pengajuan tempat magang telah direvisi');

        return redirect()->route('mahasiswa.pengajuan.show', $id)
            ->with('sukses', 'Perbaikan dikirim ulang dan menunggu verifikasi dosen PA.');
    }

    // ------------------------------------------------------------------

    /** Ambil pengajuan hanya jika milik mahasiswa ini (selain itu 404). */
    private function milik(Mahasiswa $mhs, string $id, array $with = []): PengajuanTempat
    {
        return PengajuanTempat::with($with)
            ->where('id_pengajuan', $id)
            ->whereHas('pendaftaran', fn ($q) => $q->where('id_mahasiswa', $mhs->id_mahasiswa))
            ->firstOrFail();
    }

    private function simpanBerkas(PengajuanTempat $pengajuan, Request $request): void
    {
        foreach (self::JENIS_BERKAS as $field => $label) {
            if (! $request->hasFile($field)) {
                continue;
            }
            $file = $request->file($field);

            BerkasPengajuan::create([
                'id_pengajuan'   => $pengajuan->id_pengajuan,
                'jenis_berkas'   => $label,
                'nama_berkas'    => $file->getClientOriginalName(),
                'url_berkas'     => $file->store('berkas_pengajuan', 'public'),
                'tanggal_unggah' => now(),
            ]);
        }
    }

    private function kabarkanDosen(Mahasiswa $mhs, PengajuanTempat $pengajuan, string $jenis, string $judul): void
    {
        $penerima = $mhs->dosenPa?->id_pengguna;
        if (! $penerima) {
            return;
        }

        Notifikasi::create([
            'id_pengguna'      => $penerima,
            'jenis_notifikasi' => $jenis,
            'judul'            => $judul,
            'pesan'            => $mhs->pengguna->nama_lengkap . ' (' . $mhs->nim . ') menunggu verifikasi pengajuan tempat magang.',
            'id_referensi'     => $pengajuan->id_pengajuan,
        ]);
    }

    private function pesan(): array
    {
        return [
            'required'              => 'Kolom ini wajib diisi.',
            'rencana_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'mimes'                 => 'Berkas harus berformat PDF, DOC, atau DOCX.',
            'max'                   => 'Ukuran berkas/isian terlalu besar (maksimal 5 MB untuk berkas).',
            'uploaded'              => 'Berkas gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ];
    }
}
