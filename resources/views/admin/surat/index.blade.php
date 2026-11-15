@extends('layouts.app')
@section('title', 'Surat Magang')
@section('judul', 'Surat Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach ($semuaStatus as $s)
            <a class="btn btn-kecil {{ $status === $s ? '' : 'btn-abu' }}"
               href="{{ route('admin.surat.index', ['status' => $s]) }}">{{ $s }} ({{ $jumlah[$s] ?? 0 }})</a>
        @endforeach
        <a class="btn btn-kecil {{ $status === 'semua' ? '' : 'btn-abu' }}"
           href="{{ route('admin.surat.index', ['status' => 'semua']) }}">Semua ({{ $jumlah->sum() }})</a>
    </div>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada surat dengan status ini.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>NIM</th><th>Tempat</th><th>Diminta</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $s)
                    <tr>
                        <td>{{ $s->pengajuan?->pendaftaran?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $s->pengajuan?->pendaftaran?->mahasiswa?->nim }}</td>
                        <td>{{ $s->pengajuan?->tempat?->nama_instansi }}</td>
                        <td>{{ $s->tanggal_pengajuan?->format('d/m/Y') }}</td>
                        <td>@include('partials.badge', ['status' => $s->status_surat])</td>
                        <td><a class="btn btn-kecil" href="{{ route('admin.surat.show', $s->id_surat) }}">{{ $s->status_surat === 'Selesai' ? 'Detail' : 'Proses' }}</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
