@extends('layouts.app')
@section('title', 'Data Mahasiswa')
@section('judul', 'Data Mahasiswa')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <form method="GET" action="{{ route('admin.mahasiswa.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        <input class="input" style="max-width:260px" name="q" value="{{ $q }}" placeholder="Cari nama atau NIM">
        <select class="input" style="max-width:260px" name="dosen">
            <option value="">Semua dosen PA</option>
            @foreach ($dosenList as $d)
                <option value="{{ $d->id_dosen }}" @selected($pa === $d->id_dosen)>{{ $d->pengguna?->nama_lengkap }}</option>
            @endforeach
        </select>
        <button class="btn btn-kecil" type="submit">Terapkan</button>
        <a class="btn btn-kecil btn-abu" href="{{ route('admin.mahasiswa.index') }}">Reset</a>
        <a class="btn btn-kecil" style="margin-left:auto" href="{{ route('admin.pengguna.create') }}">+ Tambah Akun</a>
    </form>

    <div class="panel">
        <h3>{{ $daftar->count() }} mahasiswa</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada data yang cocok.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>NIM</th><th>Nama</th><th>Program Studi</th><th>Angkatan</th><th>Dosen PA</th><th>Magang</th><th>Akun</th><th></th></tr>
                @foreach ($daftar as $m)
                    @php $penempatan = $m->penempatan->sortByDesc('created_at')->first(); @endphp
                    <tr>
                        <td>{{ $m->nim }}</td>
                        <td>{{ $m->pengguna?->nama_lengkap }}</td>
                        <td>{{ $m->program_studi }}</td>
                        <td>{{ $m->angkatan }}</td>
                        <td>{{ $m->dosenPa?->pengguna?->nama_lengkap ?? '-' }}</td>
                        <td>@if ($penempatan) @include('partials.badge', ['status' => $penempatan->status_magang])
                            @else <span class="badge b-abu">Belum</span> @endif</td>
                        <td><span class="badge {{ $m->pengguna?->status_aktif ? 'b-hijau' : 'b-merah' }}">{{ $m->pengguna?->status_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td><a class="btn btn-kecil btn-abu" href="{{ route('admin.pengguna.edit', $m->id_pengguna) }}">Ubah</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
