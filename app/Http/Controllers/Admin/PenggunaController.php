<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    private const ROLE = ['Mahasiswa', 'Dosen', 'Admin'];

    public function index(Request $request)
    {
        $role = $request->query('role', 'semua');
        if ($role !== 'semua' && ! in_array($role, self::ROLE, true)) {
            $role = 'semua';
        }
        $q = trim((string) $request->query('q', ''));

        $jumlah = Pengguna::selectRaw('role, count(*) as jml')->groupBy('role')->pluck('jml', 'role');

        $daftar = Pengguna::query()
            ->when($role !== 'semua', fn ($x) => $x->where('role', $role))
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w
                ->where('nama_lengkap', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('nama_lengkap')->take(200)->get();

        return view('admin.pengguna.index', compact('daftar', 'role', 'q', 'jumlah'));
    }

    public function create()
    {
        return view('admin.pengguna.form', [
            'u' => null,
            'dosenList' => $this->dosenList(),
        ]);
    }

    public function store(Request $request)
    {
        $role = $request->input('role');
        [$aturan, $pesan] = $this->aturan($role, null);
        $data = $request->validate($aturan, $pesan);

        DB::transaction(function () use ($data, $request, $role) {
            $user = Pengguna::create([
                'email'        => $data['email'],
                'password'     => $data['password'],        // di-hash otomatis oleh model
                'nama_lengkap' => $data['nama_lengkap'],
                'no_telepon'   => $data['no_telepon'] ?? null,
                'role'         => $role,
                'status_aktif' => $request->boolean('status_aktif', true),
            ]);

            $this->simpanProfil($user, $role, $data);
        });

        return redirect()->route('admin.pengguna.index', ['role' => $role])
            ->with('sukses', "Akun {$role} berhasil dibuat.");
    }

    public function edit(string $id)
    {
        $u = Pengguna::with(['dosen', 'mahasiswa'])->findOrFail($id);

        return view('admin.pengguna.form', ['u' => $u, 'dosenList' => $this->dosenList()]);
    }

    public function update(Request $request, string $id)
    {
        $u = Pengguna::with(['dosen', 'mahasiswa'])->findOrFail($id);
        $role = $u->role;                                   // role tidak dapat diubah

        [$aturan, $pesan] = $this->aturan($role, $u);
        $data = $request->validate($aturan, $pesan);

        // Admin tidak boleh menonaktifkan akunnya sendiri
        $aktif = $u->id_pengguna === $request->user()->id_pengguna
            ? true : $request->boolean('status_aktif');

        DB::transaction(function () use ($u, $data, $role, $aktif) {
            $u->fill([
                'email'        => $data['email'],
                'nama_lengkap' => $data['nama_lengkap'],
                'no_telepon'   => $data['no_telepon'] ?? null,
                'status_aktif' => $aktif,
            ]);
            if (! empty($data['password'])) {
                $u->password = $data['password'];
            }
            $u->save();

            $this->simpanProfil($u, $role, $data);
        });

        return redirect()->route('admin.pengguna.index', ['role' => $role])
            ->with('sukses', 'Data akun diperbarui.');
    }

    public function status(Request $request, string $id)
    {
        $u = Pengguna::findOrFail($id);

        if ($u->id_pengguna === $request->user()->id_pengguna) {
            return back()->with('galat', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $u->update(['status_aktif' => ! $u->status_aktif]);

        return back()->with('sukses', $u->nama_lengkap . ($u->status_aktif ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    // ------------------------------------------------------------------

    /** Aturan validasi sesuai role. $u null = akun baru. */
    private function aturan(?string $role, ?Pengguna $u): array
    {
        $baru = $u === null;

        $aturan = [
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100',
                               Rule::unique('pengguna', 'email')->ignore($u?->id_pengguna, 'id_pengguna')],
            'no_telepon'   => ['nullable', 'string', 'max:20'],
            'password'     => $baru ? ['required', 'string', 'min:8', 'max:100']
                                    : ['nullable', 'string', 'min:8', 'max:100'],
        ];

        if ($baru) {
            $aturan['role'] = ['required', Rule::in(self::ROLE)];
        }

        if ($role === 'Dosen') {
            $aturan['nidn'] = ['required', 'string', 'max:20',
                Rule::unique('dosen', 'nidn')->ignore($u?->dosen?->id_dosen, 'id_dosen')];
            $aturan['bidang_keahlian'] = ['nullable', 'string', 'max:100'];
        }

        if ($role === 'Mahasiswa') {
            $aturan['nim'] = ['required', 'string', 'max:15',
                Rule::unique('mahasiswa', 'nim')->ignore($u?->mahasiswa?->id_mahasiswa, 'id_mahasiswa')];
            $aturan['program_studi'] = ['required', 'string', 'max:50'];
            $aturan['angkatan']      = ['required', 'integer', 'min:2000', 'max:' . (now()->year + 1)];
            $aturan['id_dosen_pa']   = ['required', 'string', 'exists:dosen,id_dosen'];
        }

        $pesan = [
            'required'         => 'Kolom ini wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah dipakai akun lain.',
            'password.min'     => 'Password minimal 8 karakter.',
            'nidn.unique'      => 'NIDN sudah terdaftar.',
            'nim.unique'       => 'NIM sudah terdaftar.',
            'angkatan.integer' => 'Angkatan harus berupa angka.',
            'angkatan.min'     => 'Angkatan tidak valid.',
            'angkatan.max'     => 'Angkatan tidak valid.',
            'id_dosen_pa.exists' => 'Dosen PA tidak valid.',
            'role.in'          => 'Role tidak valid.',
        ];

        return [$aturan, $pesan];
    }

    private function simpanProfil(Pengguna $user, string $role, array $data): void
    {
        if ($role === 'Dosen') {
            Dosen::updateOrCreate(['id_pengguna' => $user->id_pengguna], [
                'nidn'            => $data['nidn'],
                'bidang_keahlian' => $data['bidang_keahlian'] ?? null,
            ]);
        } elseif ($role === 'Mahasiswa') {
            Mahasiswa::updateOrCreate(['id_pengguna' => $user->id_pengguna], [
                'id_dosen_pa'   => $data['id_dosen_pa'],
                'nim'           => $data['nim'],
                'program_studi' => $data['program_studi'],
                'angkatan'      => $data['angkatan'],
            ]);
        }
    }

    private function dosenList()
    {
        return Dosen::with('pengguna')->get()
            ->sortBy(fn ($d) => $d->pengguna?->nama_lengkap)->values();
    }
}
