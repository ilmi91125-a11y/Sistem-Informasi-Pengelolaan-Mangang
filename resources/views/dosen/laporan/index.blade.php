@extends('layouts.app')
@section('title', 'Laporan')
@section('judul', 'Laporan Mahasiswa')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach ($semuaStatus as $s)
            <a class="btn btn-kecil {{ $status === $s ? '' : 'btn-abu' }}"
               href="{{ route('dosen.laporan.index', ['status' => $s]) }}">{{ $s }} ({{ $jumlah[$s] ?? 0 }})</a>
        @endforeach
        <a class="btn btn-kecil {{ $status === 'semua' ? '' : 'btn-abu' }}"
           href="{{ route('dosen.laporan.index', ['status' => 'semua']) }}">Semua ({{ $jumlah->sum() }})</a>
    </div>

    <div class="panel">
        <h3>Laporan Mahasiswa Bimbingan Anda</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada laporan dengan status ini.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>NIM</th><th>Judul</th><th>Diunggah</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $l)
                    <tr>
                        <td>{{ $l->penempatan?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $l->penempatan?->mahasiswa?->nim }}</td>
                        <td>{{ $l->judul_laporan }}</td>
                        <td>{{ $l->tanggal_unggah?->format('d/m/Y') }}</td>
                        <td>@include('partials.badge', ['status' => $l->status_laporan])</td>
                        <td><a class="btn btn-kecil" href="{{ route('dosen.laporan.show', $l->id_laporan) }}">{{ $l->status_laporan === 'Diunggah' ? 'Periksa' : 'Detail' }}</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
