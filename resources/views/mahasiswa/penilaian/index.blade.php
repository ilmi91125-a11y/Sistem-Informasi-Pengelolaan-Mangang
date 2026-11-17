@extends('layouts.app')
@section('title', 'Penilaian')
@section('judul', 'Penilaian Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @if (! $penempatan)
        <div class="info">Anda belum memiliki penempatan magang.</div>
    @else
        <div class="panel">
            <h3>Penempatan</h3>
            <table class="rincian">
                <tr><th>Tempat</th><td>{{ $penempatan->tempat?->nama_instansi }}</td></tr>
                <tr><th>Periode</th><td>{{ $penempatan->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $penempatan->tanggal_selesai?->format('d/m/Y') }}</td></tr>
                <tr><th>Dosen Pembimbing</th><td>{{ $penempatan->dosenPembimbing?->pengguna?->nama_lengkap ?? 'Belum ditetapkan' }}</td></tr>
                <tr><th>Status</th><td>@include('partials.badge', ['status' => $penempatan->status_magang])</td></tr>
            </table>
        </div>

        @if ($nilai && $nilai->nilai_akhir !== null)
            <div class="kartu-grid">
                <div class="kartu"><div class="label">Nilai Dosen</div><div class="angka">{{ number_format($nilai->nilai_dosen, 2, ',', '.') }}</div></div>
                <div class="kartu"><div class="label">Nilai Instansi</div><div class="angka">{{ number_format($nilai->nilai_instansi, 2, ',', '.') }}</div></div>
                <div class="kartu"><div class="label">Nilai Akhir</div><div class="angka">{{ number_format($nilai->nilai_akhir, 2, ',', '.') }}</div></div>
            </div>
            <div class="panel">
                <small style="color:#6b7280">Nilai akhir = {{ $bobotDosen }}% nilai dosen + {{ $bobotInstansi }}% nilai instansi.
                    Diinput {{ $nilai->tanggal_input?->format('d/m/Y H:i') }}.</small>
                @if ($nilai->catatan)
                    <div style="margin-top:10px;font-size:14px"><strong>Catatan</strong>
                        <div style="white-space:pre-line">{{ $nilai->catatan }}</div></div>
                @endif
            </div>
        @else
            <div class="info">Nilai magang Anda belum tersedia. Anda akan mendapat notifikasi setelah dosen pembimbing memasukkan nilai.</div>
        @endif
    @endif
@endsection
