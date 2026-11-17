@extends('layouts.app')
@section('title', 'Beri Nilai')
@section('judul', 'Beri Nilai Magang')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    <div class="panel">
        <table class="rincian">
            <tr><th>Mahasiswa</th><td>{{ $p->mahasiswa?->pengguna?->nama_lengkap }} ({{ $p->mahasiswa?->nim }})</td></tr>
            <tr><th>Tempat Magang</th><td>{{ $p->tempat?->nama_instansi }}</td></tr>
            <tr><th>Periode</th><td>{{ $p->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $p->tanggal_selesai?->format('d/m/Y') }}</td></tr>
            <tr><th>Status Magang</th><td>@include('partials.badge', ['status' => $p->status_magang])</td></tr>
        </table>
    </div>

    @unless ($laporanDiterima)
        <div class="info">Catatan: laporan mahasiswa ini belum berstatus Diperiksa (diterima). Anda tetap dapat memberi nilai, tetapi sebaiknya laporan diperiksa terlebih dahulu.</div>
    @endunless

    <form method="POST" action="{{ route('dosen.penilaian.simpan', $p->id_penempatan) }}">
        @csrf @method('PUT')
        <div class="panel">
            <div class="baris">
                <div class="grup">
                    <label for="nilai_dosen">Nilai Dosen Pembimbing (0-100)</label>
                    <input class="input" type="number" step="0.01" min="0" max="100" id="nilai_dosen" name="nilai_dosen"
                           value="{{ old('nilai_dosen', $nilai?->nilai_dosen) }}" oninput="hitung()" required>
                    @error('nilai_dosen') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="nilai_instansi">Nilai Instansi (0-100)</label>
                    <input class="input" type="number" step="0.01" min="0" max="100" id="nilai_instansi" name="nilai_instansi"
                           value="{{ old('nilai_instansi', $nilai?->nilai_instansi) }}" oninput="hitung()" required>
                    @error('nilai_instansi') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label>Nilai Akhir (otomatis)</label>
                <div style="font-size:24px;font-weight:700;color:#12306b" id="akhir">-</div>
                <small>Nilai akhir = {{ $bobotDosen }}% nilai dosen + {{ $bobotInstansi }}% nilai instansi.</small>
            </div>
            <div class="grup">
                <label for="catatan">Catatan (opsional)</label>
                <textarea class="input" id="catatan" name="catatan">{{ old('catatan', $nilai?->catatan) }}</textarea>
                @error('catatan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
        </div>
        <button class="btn" type="submit">Simpan Nilai</button>
        <a class="btn btn-abu" href="{{ route('dosen.penilaian.index') }}">Batal</a>
    </form>

    <script>
        var BD = {{ $bobotDosen }} / 100, BI = {{ $bobotInstansi }} / 100;
        function hitung() {
            var d = parseFloat(document.getElementById('nilai_dosen').value);
            var i = parseFloat(document.getElementById('nilai_instansi').value);
            var el = document.getElementById('akhir');
            el.textContent = (isNaN(d) || isNaN(i)) ? '-' : (d * BD + i * BI).toFixed(2).replace('.', ',');
        }
        hitung();
    </script>
@endsection
