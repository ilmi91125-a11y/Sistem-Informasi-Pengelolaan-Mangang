@extends('layouts.app')
@section('title', 'Dashboard Akademik')
@section('judul', 'Dashboard Tim Akademik')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div class="kartu-grid">
        <div class="kartu k-biru"><div class="label">Total Dosen</div><div class="angka">{{ $totalDosen }}</div></div>
        <div class="kartu k-biru"><div class="label">Total Mahasiswa</div><div class="angka">{{ $totalMahasiswa }}</div></div>
        <div class="kartu k-oranye"><div class="label">Pengajuan Menunggu Dosen PA</div><div class="angka">{{ $pengajuanMasuk }}</div></div>
        <div class="kartu k-oranye">
            <div class="label">Surat Diproses</div>
            <div class="angka">{{ $suratDiproses }}</div>
        </div>
        <div class="kartu k-oranye">
            <div class="label">Tempat Usulan Belum Divalidasi</div>
            <div class="angka">{{ $tempatUsulan }}</div>
        </div>
        <div class="kartu k-merah">
            <div class="label">Penempatan Tanpa Pembimbing</div>
            <div class="angka">{{ $tanpaPembimbing }}</div>
        </div>
    </div>

    <div class="aksi">
        <a class="btn" href="{{ route('admin.surat.index') }}">Proses Surat ({{ $suratDiproses }})</a>
        <a class="btn btn-abu" href="{{ route('admin.tempat.index', ['filter' => 'usulan']) }}">Validasi Tempat ({{ $tempatUsulan }})</a>
        <a class="btn btn-abu" href="{{ route('admin.penempatan.create') }}">Catat Penempatan</a>
    </div>

    <div class="panel">
        <h3>Notifikasi</h3>
        @forelse ($notifikasi as $n)
            <div class="notif {{ $n->sudah_dibaca ? '' : 'baru' }}"><span class="titik"></span><div>
                <strong>{{ $n->judul }}</strong>
                @unless ($n->sudah_dibaca) <span class="badge b-biru">Baru</span> @endunless
                @if ($n->id_referensi && in_array($n->jenis_notifikasi, ['Permohonan Surat', 'Surat Dilengkapi']))
                    <a href="{{ route('admin.surat.show', $n->id_referensi) }}" style="font-size:12.5px;margin-left:6px">Buka</a>
                @endif
                <div class="isi">{{ $n->pesan }}</div>
            </div></div>
        @empty
            <div class="kosong">Belum ada notifikasi.</div>
        @endforelse
    </div>

    <div class="panel">
        <h3>Pengajuan Terbaru</h3>
        @if ($pengajuanTerbaru->isEmpty())
            <div class="kosong">Belum ada pengajuan.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Nama</th><th>NIM</th><th>Tempat Magang</th><th>Status</th></tr>
                @foreach ($pengajuanTerbaru as $p)
                    <tr>
                        <td>{{ $p->pendaftaran?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $p->pendaftaran?->mahasiswa?->nim }}</td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_pengajuan])</td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
