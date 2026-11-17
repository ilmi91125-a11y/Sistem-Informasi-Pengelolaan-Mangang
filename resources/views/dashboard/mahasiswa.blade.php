@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('judul', 'Dashboard Mahasiswa')

@section('menu')
    @include('partials.menu_mahasiswa')
@endsection

@section('isi')
    @if (! $mhs)
        <div class="info">Profil mahasiswa Anda belum dibuat oleh Tim Akademik. Hubungi administrator.</div>
    @endif

    <p class="sapa">Selamat datang, <span>{{ auth()->user()->nama_lengkap }}</span></p>

    <div class="kartu-grid">
        @php
            $statusMagang = $penempatan?->status_magang ?? $pengajuan?->status_pengajuan ?? 'Belum ada';
            $aksenStatus = match ($statusMagang) {
                'Aktif', 'Disetujui', 'Selesai' => 'k-hijau',
                'Ditolak' => 'k-merah',
                'Belum ada' => '',
                default => 'k-oranye',
            };
        @endphp
        <div class="kartu {{ $aksenStatus }}">
            <div class="label">Status Magang</div>
            <div class="teks">@include('partials.badge', ['status' => $statusMagang])</div>
        </div>
        <div class="kartu k-biru">
            <div class="label">Bimbingan</div>
            <div class="angka">{{ $jumlahBimbingan }} <small>pertemuan</small></div>
        </div>
        <div class="kartu {{ ($surat?->status_surat ?? '') === 'Selesai' ? 'k-hijau' : ($surat ? 'k-oranye' : '') }}">
            <div class="label">Surat Magang</div>
            <div class="teks">@include('partials.badge', ['status' => $surat?->status_surat ?? 'Belum ada'])</div>
        </div>
        <div class="kartu k-biru">
            <div class="label">Laporan</div>
            <div class="angka">{{ $jumlahLaporan }} <small>terkirim</small></div>
        </div>
    </div>

    <div class="aksi">
        <a class="btn" href="{{ route('mahasiswa.pendaftaran') }}">Daftar Magang</a>
        <a class="btn btn-abu" href="{{ route('mahasiswa.pengajuan.index') }}">Pengajuan Saya</a>
    </div>

    <div class="panel">
        <h3>Informasi Terbaru</h3>
        @forelse ($notifikasi as $n)
            @php
                $tautan = match ($n->jenis_notifikasi) {
                    'Hasil Verifikasi' => $n->id_referensi ? route('mahasiswa.pengajuan.show', $n->id_referensi) : null,
                    'Status Surat'     => route('mahasiswa.surat.index'),
                    'Tanggapan Bimbingan' => route('mahasiswa.bimbingan.index'),
                    'Hasil Laporan' => route('mahasiswa.laporan.index'),
                    'Nilai Magang' => route('mahasiswa.penilaian.index'),
                    default            => null,
                };
            @endphp
            <div class="notif {{ $n->sudah_dibaca ? '' : 'baru' }}">
                <span class="titik"></span>
                <div>
                    <strong>{{ $n->judul }}</strong>
                    @unless ($n->sudah_dibaca) <span class="badge b-biru">Baru</span> @endunless
                    @if ($tautan) <a href="{{ $tautan }}" style="font-size:12.5px;margin-left:6px">Lihat</a> @endif
                    <div class="isi">{{ $n->pesan }}</div>
                </div>
            </div>
        @empty
            <div class="kosong">Belum ada informasi terbaru.</div>
        @endforelse
    </div>

    <div class="baris" style="align-items:start">
        <div class="panel">
            <h3>Data Saya</h3>
            @if ($mhs)
                <table class="rincian">
                    <tr><th>NIM</th><td>{{ $mhs->nim }}</td></tr>
                    <tr><th>Program Studi</th><td>{{ $mhs->program_studi }}</td></tr>
                    <tr><th>Angkatan</th><td>{{ $mhs->angkatan }}</td></tr>
                    <tr><th>Dosen PA</th><td>{{ $mhs->dosenPa?->pengguna?->nama_lengkap ?? '-' }}</td></tr>
                </table>
            @else
                <div class="kosong">Belum ada data.</div>
            @endif
        </div>

        <div class="panel">
            <h3>Penempatan Magang</h3>
            @if ($penempatan)
                <table class="rincian">
                    <tr><th>Tempat</th><td>{{ $penempatan->tempat?->nama_instansi }}</td></tr>
                    <tr><th>Periode</th><td>{{ $penempatan->tanggal_mulai->format('d/m/Y') }} s.d. {{ $penempatan->tanggal_selesai->format('d/m/Y') }}</td></tr>
                    <tr><th>Status</th><td>@include('partials.badge', ['status' => $penempatan->status_magang])</td></tr>
                </table>
            @else
                <div class="kosong">Belum ada penempatan magang.</div>
            @endif
        </div>
    </div>
@endsection
