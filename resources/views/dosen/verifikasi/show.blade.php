@extends('layouts.app')
@section('title', 'Periksa Pengajuan')
@section('judul', 'Periksa Pengajuan')
@section('menu') @include('partials.menu_dosen') @endsection

@section('isi')
    @php $mhs = $pengajuan->pendaftaran?->mahasiswa; @endphp

    <div class="panel">
        <h3>Mahasiswa</h3>
        <table class="rincian">
            <tr><th>Nama</th><td>{{ $mhs?->pengguna?->nama_lengkap }}</td></tr>
            <tr><th>NIM</th><td>{{ $mhs?->nim }}</td></tr>
            <tr><th>Program Studi</th><td>{{ $mhs?->program_studi }} (angkatan {{ $mhs?->angkatan }})</td></tr>
            <tr><th>Periode Magang</th><td>{{ $pengajuan->pendaftaran?->periode_magang }}</td></tr>
        </table>
    </div>

    <div class="panel">
        <h3>Pengajuan</h3>
        <table class="rincian">
            <tr><th>Status</th><td>@include('partials.badge', ['status' => $pengajuan->status_pengajuan])</td></tr>
            <tr><th>Instansi</th>
                <td>{{ $pengajuan->tempat?->nama_instansi }}
                    @if ($pengajuan->tempat?->sumber_data === 'Mahasiswa')
                        <span class="badge b-kuning">Usulan mahasiswa, belum divalidasi</span>
                    @endif
                </td></tr>
            <tr><th>Alamat</th><td>{{ $pengajuan->tempat?->alamat }}</td></tr>
            @if ($pengajuan->tempat?->bidang_usaha)
                <tr><th>Bidang Usaha</th><td>{{ $pengajuan->tempat->bidang_usaha }}</td></tr>
            @endif
            @if ($pengajuan->tempat?->kontak)
                <tr><th>Kontak</th><td>{{ $pengajuan->tempat->kontak }}</td></tr>
            @endif
            <tr><th>Rencana Kegiatan</th><td style="white-space:pre-line">{{ $pengajuan->rencana_kegiatan }}</td></tr>
            <tr><th>Rencana Pelaksanaan</th><td>{{ $pengajuan->rencana_mulai?->format('d/m/Y') }} s.d. {{ $pengajuan->rencana_selesai?->format('d/m/Y') }}</td></tr>
            <tr><th>Tanggal Pengajuan</th><td>{{ $pengajuan->tanggal_pengajuan?->format('d/m/Y H:i') }}</td></tr>
        </table>
    </div>

    <div class="panel">
        <h3>Berkas</h3>
        @forelse ($pengajuan->berkas as $b)
            <div style="padding:6px 0;border-bottom:1px solid #e5e7eb;font-size:14px">
                <strong>{{ $b->jenis_berkas }}</strong> -
                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($b->url_berkas) }}" target="_blank">{{ $b->nama_berkas }}</a>
                <span style="color:#6b7280">({{ $b->tanggal_unggah?->format('d/m/Y') }})</span>
            </div>
        @empty
            <div class="kosong">Tidak ada berkas.</div>
        @endforelse
    </div>

    @if ($pengajuan->status_pengajuan === 'Diajukan')
        <div class="panel">
            <h3>Keputusan</h3>
            <form method="POST" action="{{ route('dosen.verifikasi.store', $pengajuan->id_pengajuan) }}">
                @csrf
                <div class="opsi" style="flex-wrap:wrap">
                    @foreach (['Disetujui' => 'Setujui', 'Revisi' => 'Minta Revisi', 'Ditolak' => 'Tolak'] as $nilai => $label)
                        <label><input type="radio" name="keputusan" value="{{ $nilai }}" @checked(old('keputusan') === $nilai) onchange="ubahKeputusan()" required> {{ $label }}</label>
                    @endforeach
                </div>
                @error('keputusan') <div class="galat-field">{{ $message }}</div> @enderror

                <div class="grup" style="margin-top:12px">
                    <label for="catatan">Catatan <span id="wajib" style="color:#b91c1c;display:none">* wajib untuk Revisi/Tolak</span></label>
                    <textarea class="input" id="catatan" name="catatan" placeholder="Tuliskan alasan atau bagian yang perlu diperbaiki">{{ old('catatan') }}</textarea>
                    @error('catatan') <div class="galat-field">{{ $message }}</div> @enderror
                </div>

                <button class="btn" type="submit" onclick="return confirm('Simpan keputusan ini? Keputusan tidak dapat diubah.')">Simpan Keputusan</button>
                <a class="btn btn-abu" href="{{ route('dosen.verifikasi.index') }}">Kembali</a>
            </form>
        </div>
        <script>
            function ubahKeputusan() {
                var pilih = document.querySelector('input[name="keputusan"]:checked');
                var wajib = pilih && pilih.value !== 'Disetujui';
                document.getElementById('wajib').style.display = wajib ? 'inline' : 'none';
                document.getElementById('catatan').required = !!wajib;
            }
            ubahKeputusan();
        </script>
    @else
        <a class="btn btn-abu" href="{{ route('dosen.verifikasi.index') }}">Kembali</a>
    @endif

    <div class="panel" style="margin-top:20px">
        <h3>Riwayat Verifikasi</h3>
        @forelse ($riwayat as $v)
            <div style="padding:8px 0;border-bottom:1px solid #e5e7eb;font-size:14px">
                @include('partials.badge', ['status' => $v->keputusan])
                <span style="color:#6b7280">{{ $v->tanggal_verifikasi?->format('d/m/Y H:i') }} - {{ $v->dosen?->pengguna?->nama_lengkap }}</span>
                @if ($v->catatan)
                    <div style="margin-top:6px;white-space:pre-line">{{ $v->catatan }}</div>
                @endif
            </div>
        @empty
            <div class="kosong">Belum ada riwayat verifikasi.</div>
        @endforelse
    </div>
@endsection
