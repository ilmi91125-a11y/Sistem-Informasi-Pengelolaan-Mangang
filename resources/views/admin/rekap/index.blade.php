@extends('layouts.app')
@section('title', 'Rekapitulasi')
@section('judul', 'Rekapitulasi Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div class="kartu-grid">
        <div class="kartu"><div class="label">Total Penempatan</div><div class="angka">{{ $ringkasan['total'] }}</div></div>
        <div class="kartu"><div class="label">Berjalan</div><div class="angka">{{ $ringkasan['berjalan'] }}</div></div>
        <div class="kartu"><div class="label">Selesai</div><div class="angka">{{ $ringkasan['selesai'] }}</div></div>
        <div class="kartu"><div class="label">Sudah Dinilai</div><div class="angka">{{ $ringkasan['dinilai'] }}</div></div>
    </div>

    <form method="GET" action="{{ route('admin.rekap.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        <input class="input" style="max-width:230px" name="q" value="{{ $f['q'] }}" placeholder="Cari nama atau NIM">
        <select class="input" style="max-width:170px" name="status">
            @foreach (['semua' => 'Semua status', 'Berjalan' => 'Berjalan', 'Selesai' => 'Selesai'] as $k => $v)
                <option value="{{ $k }}" @selected($f['status'] === $k)>{{ $v }}</option>
            @endforeach
        </select>
        <select class="input" style="max-width:250px" name="dosen">
            <option value="">Semua dosen pembimbing</option>
            @foreach ($dosenList as $d)
                <option value="{{ $d->id_dosen }}" @selected($f['dosen'] === $d->id_dosen)>{{ $d->pengguna?->nama_lengkap }}</option>
            @endforeach
        </select>
        <button class="btn btn-kecil" type="submit">Terapkan</button>
        <a class="btn btn-kecil btn-abu" href="{{ route('admin.rekap.index') }}">Reset</a>
        <a class="btn btn-kecil" style="margin-left:auto"
           href="{{ route('admin.rekap.ekspor', array_filter($f, fn ($v) => $v !== '' && $v !== 'semua')) }}">Unduh CSV (Excel)</a>
    </form>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Tidak ada data yang cocok.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>NIM</th><th>Nama</th><th>Tempat</th><th>Periode</th><th>Pembimbing</th><th>Status</th>
                    <th>Bimbingan</th><th>Laporan</th><th>Nilai Akhir</th></tr>
                @foreach ($daftar as $p)
                    @php $laporan = $p->laporan->sortByDesc('tanggal_unggah')->first(); $akhir = $p->penilaian?->nilai_akhir; @endphp
                    <tr>
                        <td>{{ $p->mahasiswa?->nim }}</td>
                        <td>{{ $p->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td style="white-space:nowrap">{{ $p->tanggal_mulai?->format('d/m/Y') }} - {{ $p->tanggal_selesai?->format('d/m/Y') }}</td>
                        <td>{{ $p->dosenPembimbing?->pengguna?->nama_lengkap ?? '-' }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_magang])</td>
                        <td>{{ $p->bimbingan_count }}</td>
                        <td>@if ($laporan) @include('partials.badge', ['status' => $laporan->status_laporan]) @else - @endif</td>
                        <td>{{ $akhir !== null ? number_format($akhir, 2, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
