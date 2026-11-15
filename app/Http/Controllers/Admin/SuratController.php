<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\SuratMagang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuratController extends Controller
{
    private const STATUS = ['Diproses', 'Perlu Dilengkapi', 'Selesai'];

    public function index(Request $request)
    {
        $status = $request->query('status', 'Diproses');
        if ($status !== 'semua' && ! in_array($status, self::STATUS, true)) {
            $status = 'Diproses';
        }

        $jumlah = SuratMagang::selectRaw('status_surat, count(*) as jml')
            ->groupBy('status_surat')->pluck('jml', 'status_surat');

        $daftar = SuratMagang::with(['pengajuan.pendaftaran.mahasiswa.pengguna', 'pengajuan.tempat'])
            ->when($status !== 'semua', fn ($q) => $q->where('status_surat', $status))
            ->latest('tanggal_pengajuan')->take(100)->get();

        return view('admin.surat.index', [
            'daftar' => $daftar, 'status' => $status, 'jumlah' => $jumlah, 'semuaStatus' => self::STATUS,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $surat = SuratMagang::with([
            'pengajuan.tempat', 'pengajuan.berkas', 'pengajuan.pendaftaran.mahasiswa.pengguna', 'pemroses',
        ])->findOrFail($id);

        Notifikasi::where('id_pengguna', $request->user()->id_pengguna)
            ->where('id_referensi', $surat->id_surat)->update(['sudah_dibaca' => true]);

        return view('admin.surat.show', compact('surat'));
    }

    public function update(Request $request, string $id)
    {
        $surat = SuratMagang::with(['pengajuan.tempat', 'pengajuan.pendaftaran.mahasiswa.pengguna'])->findOrFail($id);

        if ($surat->status_surat === 'Selesai') {
            return back()->with('galat', 'Surat yang sudah Selesai tidak dapat diubah lagi.');
        }

        $status = $request->input('status_surat');

        $data = $request->validate([
            'status_surat' => ['required', Rule::in(self::STATUS)],
            'nomor_surat'  => [Rule::requiredIf($status === 'Selesai'), 'nullable', 'string', 'max:50'],
            'catatan'      => [Rule::requiredIf($status === 'Perlu Dilengkapi'), 'nullable', 'string', 'max:2000'],
            'file_surat'   => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'status_surat.required' => 'Pilih status surat.',
            'nomor_surat.required'  => 'Nomor surat wajib diisi untuk status Selesai.',
            'catatan.required'      => 'Catatan wajib diisi agar mahasiswa tahu apa yang harus dilengkapi.',
            'file_surat.mimes'      => 'Berkas surat harus berformat PDF, DOC, atau DOCX.',
            'file_surat.max'        => 'Ukuran berkas surat maksimal 5 MB.',
            'file_surat.uploaded'   => 'Berkas gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ]);

        $url = $surat->url_berkas;
        if ($request->hasFile('file_surat')) {
            $url = $request->file('file_surat')->store('surat_magang', 'public');
        }

        if ($data['status_surat'] === 'Selesai' && ! $url) {
            return back()->withInput()->withErrors(['file_surat' => 'Unggah berkas surat untuk status Selesai.']);
        }

        $surat->update([
            'id_pemroses'     => $request->user()->id_pengguna,
            'status_surat'    => $data['status_surat'],
            'nomor_surat'     => $data['nomor_surat'] ?? $surat->nomor_surat,
            'catatan'         => $data['catatan'] ?? null,
            'url_berkas'      => $url,
            'tanggal_selesai' => $data['status_surat'] === 'Selesai' ? now()->toDateString() : null,
        ]);

        $mhs = $surat->pengajuan->pendaftaran->mahasiswa;
        $instansi = $surat->pengajuan->tempat?->nama_instansi;

        $pesan = match ($data['status_surat']) {
            'Selesai'          => "Surat magang untuk {$instansi} sudah selesai. Silakan unduh di menu Surat Magang.",
            'Perlu Dilengkapi' => "Surat magang untuk {$instansi} perlu dilengkapi. Catatan: {$data['catatan']}",
            default            => "Surat magang untuk {$instansi} sedang diproses.",
        };

        Notifikasi::create([
            'id_pengguna'      => $mhs->id_pengguna,
            'jenis_notifikasi' => 'Status Surat',
            'judul'            => 'Surat magang: ' . $data['status_surat'],
            'pesan'            => $pesan,
            'id_referensi'     => $surat->id_surat,
        ]);

        return redirect()->route('admin.surat.show', $id)->with('sukses', 'Status surat diperbarui dan mahasiswa telah diberi tahu.');
    }
}
