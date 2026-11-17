@extends('layouts.app')
@section('title', 'Penilaian')
@section('judul', 'Penilaian Magang')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    <div class="panel">
        <h3>Mahasiswa Bimbingan Anda</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Belum ada mahasiswa yang Anda bimbing.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>NIM</th><th>Tempat</th><th>Magang</th><th>Laporan</th><th>Nilai Akhir</th><th></th></tr>
                @foreach ($daftar as $p)
                    @php
                        $laporan = $p->laporan->sortByDesc('tanggal_unggah')->first();
                        $akhir = $p->penilaian?->nilai_akhir;
                    @endphp
                    <tr>
                        <td>{{ $p->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $p->mahasiswa?->nim }}</td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_magang])</td>
                        <td>@if ($laporan) @include('partials.badge', ['status' => $laporan->status_laporan])
                            @else <span class="badge b-abu">Belum ada</span> @endif</td>
                        <td>@if ($akhir !== null) <strong>{{ number_format($akhir, 2, ',', '.') }}</strong>
                            @else <span class="badge b-kuning">Belum dinilai</span> @endif</td>
                        <td><a class="btn btn-kecil" href="{{ route('dosen.penilaian.edit', $p->id_penempatan) }}">{{ $akhir !== null ? 'Ubah Nilai' : 'Beri Nilai' }}</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
