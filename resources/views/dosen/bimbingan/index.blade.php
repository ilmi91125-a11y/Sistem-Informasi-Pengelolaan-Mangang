@extends('layouts.app')
@section('title', 'Bimbingan')
@section('judul', 'Bimbingan Mahasiswa')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach ($semuaStatus as $s)
            <a class="btn btn-kecil {{ $status === $s ? '' : 'btn-abu' }}"
               href="{{ route('dosen.bimbingan.index', ['status' => $s]) }}">{{ $s }} ({{ $jumlah[$s] ?? 0 }})</a>
        @endforeach
        <a class="btn btn-kecil {{ $status === 'semua' ? '' : 'btn-abu' }}"
           href="{{ route('dosen.bimbingan.index', ['status' => 'semua']) }}">Semua ({{ $jumlah->sum() }})</a>
    </div>

    <div class="panel">
        <h3>Bimbingan Mahasiswa Anda</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada catatan bimbingan dengan status ini.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>NIM</th><th>Tempat</th><th>Tanggal</th><th>Topik</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $b)
                    <tr>
                        <td>{{ $b->penempatan?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $b->penempatan?->mahasiswa?->nim }}</td>
                        <td>{{ $b->penempatan?->tempat?->nama_instansi }}</td>
                        <td>{{ $b->tanggal_bimbingan?->format('d/m/Y') }}</td>
                        <td>{{ $b->topik }}</td>
                        <td>@include('partials.badge', ['status' => $b->status_bimbingan])</td>
                        <td><a class="btn btn-kecil" href="{{ route('dosen.bimbingan.show', $b->id_bimbingan) }}">{{ $b->status_bimbingan === 'Menunggu' ? 'Tanggapi' : 'Detail' }}</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
