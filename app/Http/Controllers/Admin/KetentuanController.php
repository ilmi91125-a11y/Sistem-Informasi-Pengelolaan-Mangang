<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KetentuanMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KetentuanController extends Controller
{
    public function index()
    {
        $daftar = KetentuanMagang::latest('created_at')->get();

        return view('admin.ketentuan.index', compact('daftar'));
    }

    public function create()
    {
        return view('admin.ketentuan.form', ['k' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $url = $request->hasFile('berkas')
            ? $request->file('berkas')->store('ketentuan', 'public') : null;

        KetentuanMagang::create([
            'id_pengguna'   => $request->user()->id_pengguna,
            'judul'         => $data['judul'],
            'isi_ketentuan' => $data['isi_ketentuan'],
            'url_berkas'    => $url,
        ]);

        return redirect()->route('admin.ketentuan.index')->with('sukses', 'Ketentuan magang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $k = KetentuanMagang::findOrFail($id);

        return view('admin.ketentuan.form', compact('k'));
    }

    public function update(Request $request, string $id)
    {
        $k = KetentuanMagang::findOrFail($id);
        $data = $this->validasi($request);

        $url = $k->url_berkas;
        if ($request->boolean('hapus_berkas')) {
            $this->hapusFile($url);
            $url = null;
        }
        if ($request->hasFile('berkas')) {
            $this->hapusFile($url);
            $url = $request->file('berkas')->store('ketentuan', 'public');
        }

        $k->update([
            'judul'         => $data['judul'],
            'isi_ketentuan' => $data['isi_ketentuan'],
            'url_berkas'    => $url,
        ]);

        return redirect()->route('admin.ketentuan.index')->with('sukses', 'Ketentuan magang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $k = KetentuanMagang::findOrFail($id);
        $this->hapusFile($k->url_berkas);
        $k->delete();

        return redirect()->route('admin.ketentuan.index')->with('sukses', 'Ketentuan magang dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul'         => ['required', 'string', 'max:150'],
            'isi_ketentuan' => ['required', 'string'],
            'berkas'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'judul.required'         => 'Judul wajib diisi.',
            'judul.max'              => 'Judul maksimal 150 karakter.',
            'isi_ketentuan.required' => 'Isi ketentuan wajib diisi.',
            'berkas.mimes'           => 'Berkas harus berformat PDF, DOC, atau DOCX.',
            'berkas.max'             => 'Ukuran berkas maksimal 5 MB.',
            'berkas.uploaded'        => 'Berkas gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ]);
    }

    /** Hapus berkas lokal (alamat http/https dibiarkan). */
    private function hapusFile(?string $path): void
    {
        if ($path && ! preg_match('#^https?://#i', $path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
