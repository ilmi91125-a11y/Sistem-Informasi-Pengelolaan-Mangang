@extends('layouts.app')
@section('title', 'Pengajuan Lokasi')
@section('judul', 'Pengajuan Lokasi Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    <p><a class="btn" href="{{ route('mahasiswa.pengajuan.create') }}">+ Ajukan Tempat Baru</a></p>

    <div class="panel">
        <h3>Pengajuan Saya</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Belum ada pengajuan. Daftar magang dulu, lalu ajukan tempat.</div>
        @else
            <table>
                <tr><th>Tempat</th><th>Periode</th><th>Tanggal Pengajuan</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $p)
                    <tr>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>{{ $p->pendaftaran?->periode_magang }}</td>
                        <td>{{ $p->tanggal_pengajuan?->format('d/m/Y') }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_pengajuan])</td>
                        <td><a class="btn btn-kecil" href="{{ route('mahasiswa.pengajuan.show', $p->id_pengajuan) }}">Detail</a></td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection
