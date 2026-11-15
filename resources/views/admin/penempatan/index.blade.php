@extends('layouts.app')
@section('title', 'Penempatan & Jadwal')
@section('judul', 'Penempatan dan Jadwal Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <p><a class="btn" href="{{ route('admin.penempatan.create') }}">+ Catat Penempatan</a></p>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Belum ada penempatan.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Mahasiswa</th><th>Tempat</th><th>Periode</th><th>Dosen Pembimbing</th><th>Status</th><th></th></tr>
                @foreach ($daftar as $p)
                    <tr>
                        <td>{{ $p->mahasiswa?->pengguna?->nama_lengkap }}<br><small style="color:#6b7280">{{ $p->mahasiswa?->nim }}</small></td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>{{ $p->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $p->tanggal_selesai?->format('d/m/Y') }}</td>
                        <td>{{ $p->dosenPembimbing?->pengguna?->nama_lengkap ?? '-' }}
                            @unless ($p->id_dosen_pembimbing) <span class="badge b-kuning">Belum ada</span> @endunless</td>
                        <td>@include('partials.badge', ['status' => $p->status_magang])</td>
                        <td><a class="btn btn-kecil" href="{{ route('admin.penempatan.edit', $p->id_penempatan) }}">Ubah</a></td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
