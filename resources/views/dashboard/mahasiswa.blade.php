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

    <p>
        <a class="btn" href="{{ route('mahasiswa.pendaftaran') }}">Daftar Magang</a>
        <a class="btn btn-abu" href="{{ route('mahasiswa.pengajuan.index') }}">Pengajuan Saya</a>
    </p>

    <div class="kartu-grid">
        <div class="kartu">
            <div class="label">Status Pengajuan Tempat</div>
            <div class="teks">@include('partials.badge', ['status' => $pengajuan?->status_pengajuan ?? 'Belum ada'])</div>
        </div>
        <div class="kartu">
            <div class="label">Bimbingan</div>
            <div class="angka">{{ $jumlahBimbingan }}</div>
        </div>
        <div class="kartu">
            <div class="label">Surat Magang</div>
            <div class="teks">@include('partials.badge', ['status' => $surat?->status_surat ?? 'Belum ada'])</div>
        </div>
        <div class="kartu">
            <div class="label">Laporan</div>
            <div class="angka">{{ $jumlahLaporan }}</div>
        </div>
    </div>

    <div class="panel">
        <h3>Data Saya</h3>
        @if ($mhs)
            <table>
                <tr><th style="width:180px">NIM</th><td>{{ $mhs->nim }}</td></tr>
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
            <table>
                <tr><th style="width:180px">Tempat</th><td>{{ $penempatan->tempat?->nama_instansi }}</td></tr>
                <tr><th>Periode</th><td>{{ $penempatan->tanggal_mulai->format('d/m/Y') }} s.d. {{ $penempatan->tanggal_selesai->format('d/m/Y') }}</td></tr>
                <tr><th>Status</th><td>@include('partials.badge', ['status' => $penempatan->status_magang])</td></tr>
            </table>
        @else
            <div class="kosong">Belum ada penempatan magang.</div>
        @endif
    </div>

    <div class="panel">
        <h3>Informasi Terbaru</h3>
        @forelse ($notifikasi as $n)
            <div style="padding:8px 0;border-bottom:1px solid #e5e7eb">
                <strong>{{ $n->judul }}</strong>
                @unless ($n->sudah_dibaca) <span class="badge b-biru">Baru</span> @endunless
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
                @if ($tautan)
                    <a href="{{ $tautan }}" style="font-size:13px">Lihat</a>
                @endif
                <br>
                <span style="font-size:14px;color:#4b5563">{{ $n->pesan }}</span>
            </div>
        @empty
            <div class="kosong">Belum ada notifikasi.</div>
        @endforelse
    </div>
@endsection
