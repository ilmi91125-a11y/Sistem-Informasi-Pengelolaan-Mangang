<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TempatMagang;
use Illuminate\Http\Request;

class TempatController extends Controller
{
    private const FILTER = ['semua', 'aktif', 'nonaktif', 'usulan'];

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua');
        if (! in_array($filter, self::FILTER, true)) {
            $filter = 'semua';
        }

        $jumlah = [
            'semua'    => TempatMagang::count(),
            'aktif'    => TempatMagang::where('status_aktif', true)->count(),
            'nonaktif' => TempatMagang::where('status_aktif', false)->count(),
            'usulan'   => $this->usulan()->count(),
        ];

        $daftar = TempatMagang::query()
            ->when($filter === 'aktif', fn ($q) => $q->where('status_aktif', true))
            ->when($filter === 'nonaktif', fn ($q) => $q->where('status_aktif', false))
            ->when($filter === 'usulan', fn ($q) => $q->where('sumber_data', 'Mahasiswa')->where('status_aktif', false))
            ->orderBy('nama_instansi')->get();

        return view('admin.tempat.index', compact('daftar', 'filter', 'jumlah'));
    }

    public function create()
    {
        return view('admin.tempat.form', ['t' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        TempatMagang::create($data + ['sumber_data' => 'Admin']);

        return redirect()->route('admin.tempat.index')->with('sukses', 'Tempat magang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $t = TempatMagang::findOrFail($id);

        return view('admin.tempat.form', compact('t'));
    }

    public function update(Request $request, string $id)
    {
        $t = TempatMagang::findOrFail($id);
        $t->update($this->validasi($request));

        return redirect()->route('admin.tempat.index')->with('sukses', 'Data tempat magang diperbarui.');
    }

    /** Aktifkan/nonaktifkan cepat dari daftar (dipakai juga untuk memvalidasi usulan mahasiswa). */
    public function status(string $id)
    {
        $t = TempatMagang::findOrFail($id);
        $t->update(['status_aktif' => ! $t->status_aktif]);

        return back()->with('sukses', $t->nama_instansi . ($t->status_aktif ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    private function usulan()
    {
        return TempatMagang::where('sumber_data', 'Mahasiswa')->where('status_aktif', false);
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:150'],
            'alamat'        => ['required', 'string'],
            'bidang_usaha'  => ['nullable', 'string', 'max:100'],
            'kontak'        => ['nullable', 'string', 'max:100'],
            'deskripsi'     => ['nullable', 'string'],
        ], [
            'nama_instansi.required' => 'Nama instansi wajib diisi.',
            'alamat.required'        => 'Alamat wajib diisi.',
        ]);

        $data['status_aktif'] = $request->boolean('status_aktif');

        return $data;
    }
}
