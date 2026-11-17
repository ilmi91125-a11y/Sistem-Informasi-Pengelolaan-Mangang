@extends('layouts.app')
@section('title', 'Tanggapi Bimbingan')
@section('judul', 'Tanggapi Bimbingan')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    @php $mhs = $b->penempatan?->mahasiswa; @endphp

    <div class="panel">
        <h3>Catatan Mahasiswa</h3>
        <table class="rincian">
            <tr><th>Mahasiswa</th><td>{{ $mhs?->pengguna?->nama_lengkap }} ({{ $mhs?->nim }})</td></tr>
            <tr><th>Tempat Magang</th><td>{{ $b->penempatan?->tempat?->nama_instansi }}</td></tr>
            <tr><th>Tanggal</th><td>{{ $b->tanggal_bimbingan?->format('d/m/Y') }}</td></tr>
            <tr><th>Topik</th><td>{{ $b->topik }}</td></tr>
            <tr><th>Uraian Kegiatan</th><td style="white-space:pre-line">{{ $b->uraian_kegiatan }}</td></tr>
            <tr><th>Status</th><td>@include('partials.badge', ['status' => $b->status_bimbingan])</td></tr>
        </table>
    </div>

    <div class="panel">
        <h3>{{ $b->status_bimbingan === 'Menunggu' ? 'Tanggapan' : 'Tanggapan Anda (dapat diperbarui)' }}</h3>
        <form method="POST" action="{{ route('dosen.bimbingan.tanggapi', $b->id_bimbingan) }}">
            @csrf
            <div class="grup">
                <textarea class="input" name="tanggapan_dosen" style="min-height:140px" required
                          placeholder="Tuliskan masukan, arahan, atau evaluasi">{{ old('tanggapan_dosen', $b->tanggapan_dosen) }}</textarea>
                @error('tanggapan_dosen') <div class="galat-field">{{ $message }}</div> @enderror
                @if ($b->tanggal_tanggapan) <small>Terakhir ditanggapi {{ $b->tanggal_tanggapan->format('d/m/Y H:i') }}</small> @endif
            </div>
            <button class="btn" type="submit">Simpan Tanggapan</button>
            <a class="btn btn-abu" href="{{ route('dosen.bimbingan.index') }}">Kembali</a>
        </form>
    </div>
@endsection
