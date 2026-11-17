@extends('layouts.app')
@section('title', 'Periksa Laporan')
@section('judul', 'Periksa Laporan')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    @php $mhs = $laporan->penempatan?->mahasiswa; @endphp

    <div class="panel">
        <h3>Laporan</h3>
        <table class="rincian">
            <tr><th>Mahasiswa</th><td>{{ $mhs?->pengguna?->nama_lengkap }} ({{ $mhs?->nim }})</td></tr>
            <tr><th>Tempat Magang</th><td>{{ $laporan->penempatan?->tempat?->nama_instansi }}</td></tr>
            <tr><th>Judul</th><td>{{ $laporan->judul_laporan }}</td></tr>
            <tr><th>Diunggah</th><td>{{ $laporan->tanggal_unggah?->format('d/m/Y H:i') }}</td></tr>
            <tr><th>Status</th><td>@include('partials.badge', ['status' => $laporan->status_laporan])</td></tr>
            <tr><th>Berkas</th><td><a class="btn btn-kecil" target="_blank"
                href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($laporan->url_berkas) }}">Buka Berkas</a></td></tr>
        </table>
    </div>

    @if ($laporan->status_laporan === 'Diunggah')
        <div class="panel">
            <h3>Keputusan</h3>
            <form method="POST" action="{{ route('dosen.laporan.periksa', $laporan->id_laporan) }}">
                @csrf
                <div class="opsi">
                    <label><input type="radio" name="keputusan" value="Diperiksa" @checked(old('keputusan') === 'Diperiksa') onchange="ubahKeputusan()" required> Terima</label>
                    <label><input type="radio" name="keputusan" value="Revisi" @checked(old('keputusan') === 'Revisi') onchange="ubahKeputusan()"> Minta Revisi</label>
                </div>
                @error('keputusan') <div class="galat-field">{{ $message }}</div> @enderror

                <div class="grup" style="margin-top:12px">
                    <label for="catatan_dosen">Catatan <span id="wajib" style="color:#b91c1c;display:none">* wajib untuk Revisi</span></label>
                    <textarea class="input" id="catatan_dosen" name="catatan_dosen" placeholder="Tuliskan bagian yang perlu diperbaiki atau masukan lain">{{ old('catatan_dosen') }}</textarea>
                    @error('catatan_dosen') <div class="galat-field">{{ $message }}</div> @enderror
                </div>

                <button class="btn" type="submit" onclick="return confirm('Simpan hasil pemeriksaan? Keputusan tidak dapat diubah.')">Simpan</button>
                <a class="btn btn-abu" href="{{ route('dosen.laporan.index') }}">Kembali</a>
            </form>
        </div>
        <script>
            function ubahKeputusan() {
                var p = document.querySelector('input[name="keputusan"]:checked');
                var wajib = p && p.value === 'Revisi';
                document.getElementById('wajib').style.display = wajib ? 'inline' : 'none';
                document.getElementById('catatan_dosen').required = !!wajib;
            }
            ubahKeputusan();
        </script>
    @else
        <div class="panel">
            <h3>Hasil Pemeriksaan</h3>
            <div style="white-space:pre-line;font-size:14px">{{ $laporan->catatan_dosen ?: 'Tanpa catatan.' }}</div>
        </div>
        <a class="btn btn-abu" href="{{ route('dosen.laporan.index') }}">Kembali</a>
    @endif
@endsection
