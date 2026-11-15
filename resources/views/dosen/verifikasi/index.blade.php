@extends('layouts.app')
@section('title', 'Verifikasi Pengajuan')
@section('judul', 'Verifikasi Pengajuan Tempat Magang')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach ($semuaStatus as $s)
            <a class="btn btn-kecil {{ $status === $s ? '' : 'btn-abu' }}"
               href="{{ route('dosen.verifikasi.index', ['status' => $s]) }}">{{ $s }} ({{ $jumlah[$s] ?? 0 }})</a>
        @endforeach
        <a class="btn btn-kecil {{ $status === 'semua' ? '' : 'btn-abu' }}"
           href="{{ route('dosen.verifikasi.index', ['status' => 'semua']) }}">Semua ({{ $jumlah->sum() }})</a>
    </div>

    <div class="panel">
        <h3>Pengajuan Mahasiswa PA Anda</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada pengajuan dengan status ini.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>NIM</th><th>Tempat Magang</th><th>Tanggal</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $p)
                    <tr>
                        <td>{{ $p->pendaftaran?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $p->pendaftaran?->mahasiswa?->nim }}</td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>{{ $p->tanggal_pengajuan?->format('d/m/Y') }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_pengajuan])</td>
                        <td><a class="btn btn-kecil" href="{{ route('dosen.verifikasi.show', $p->id_pengajuan) }}">
                            {{ $p->status_pengajuan === 'Diajukan' ? 'Periksa' : 'Detail' }}</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
