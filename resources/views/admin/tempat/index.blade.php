@extends('layouts.app')
@section('title', 'Tempat Magang')
@section('judul', 'Tempat Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center">
        @foreach (['semua' => 'Semua', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'usulan' => 'Usulan Mahasiswa'] as $kode => $label)
            <a class="btn btn-kecil {{ $filter === $kode ? '' : 'btn-abu' }}"
               href="{{ route('admin.tempat.index', ['filter' => $kode]) }}">{{ $label }} ({{ $jumlah[$kode] }})</a>
        @endforeach
        <a class="btn btn-kecil" style="margin-left:auto" href="{{ route('admin.tempat.create') }}">+ Tambah Tempat</a>
    </div>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada data.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Instansi</th><th>Bidang</th><th>Sumber</th><th>Status</th><th style="width:190px"></th></tr>
                @foreach ($daftar as $t)
                    <tr>
                        <td>{{ $t->nama_instansi }}<br><small style="color:#6b7280">{{ $t->alamat }}</small></td>
                        <td>{{ $t->bidang_usaha ?: '-' }}</td>
                        <td>{{ $t->sumber_data }}</td>
                        <td>@include('partials.badge', ['status' => $t->status_aktif ? 'Terdaftar' : 'Dibatalkan'])
                            <small>{{ $t->status_aktif ? 'Aktif' : 'Nonaktif' }}</small></td>
                        <td style="white-space:nowrap">
                            <a class="btn btn-kecil btn-abu" href="{{ route('admin.tempat.edit', $t->id_tempat) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.tempat.status', $t->id_tempat) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-kecil" type="submit">{{ $t->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
