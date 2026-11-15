@extends('layouts.app')
@section('title', 'Pengguna & Role')
@section('judul', 'Pengguna dan Role')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;align-items:center">
        <a class="btn btn-kecil {{ $role === 'semua' ? '' : 'btn-abu' }}" href="{{ route('admin.pengguna.index', ['q' => $q]) }}">Semua ({{ $jumlah->sum() }})</a>
        @foreach (['Mahasiswa', 'Dosen', 'Admin'] as $r)
            <a class="btn btn-kecil {{ $role === $r ? '' : 'btn-abu' }}"
               href="{{ route('admin.pengguna.index', ['role' => $r, 'q' => $q]) }}">{{ $r }} ({{ $jumlah[$r] ?? 0 }})</a>
        @endforeach
        <a class="btn btn-kecil" style="margin-left:auto" href="{{ route('admin.pengguna.create') }}">+ Tambah Akun</a>
    </div>

    <form method="GET" action="{{ route('admin.pengguna.index') }}" style="display:flex;gap:8px;margin-bottom:16px">
        @if ($role !== 'semua') <input type="hidden" name="role" value="{{ $role }}"> @endif
        <input class="input" style="max-width:320px" name="q" value="{{ $q }}" placeholder="Cari nama atau email">
        <button class="btn btn-kecil" type="submit">Cari</button>
    </form>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada akun yang cocok.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th style="width:190px"></th></tr>
                @foreach ($daftar as $u)
                    <tr>
                        <td>{{ $u->nama_lengkap }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->role }}</td>
                        <td><span class="badge {{ $u->status_aktif ? 'b-hijau' : 'b-merah' }}">{{ $u->status_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td style="white-space:nowrap">
                            <a class="btn btn-kecil btn-abu" href="{{ route('admin.pengguna.edit', $u->id_pengguna) }}">Ubah</a>
                            @if ($u->id_pengguna !== auth()->user()->id_pengguna)
                                <form method="POST" action="{{ route('admin.pengguna.status', $u->id_pengguna) }}" style="display:inline"
                                      onsubmit="return confirm('{{ $u->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-kecil" type="submit">{{ $u->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
