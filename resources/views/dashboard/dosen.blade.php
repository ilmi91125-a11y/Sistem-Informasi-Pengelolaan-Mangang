@extends('layouts.app')
@section('title', 'Dashboard Dosen')
@section('judul', 'Dashboard Dosen')

@section('menu')
    @include('partials.menu_dosen')
@endsection

@section('isi')
    @if (! $dosen)
        <div class="info">Profil dosen Anda belum dibuat oleh Tim Akademik. Hubungi administrator.</div>
    @endif

    <div class="aksi">
        <a class="btn" href="{{ route('dosen.verifikasi.index') }}">Verifikasi Pengajuan ({{ $pengajuanMenunggu }})</a>
    </div>

    <div class="kartu-grid">
        <div class="kartu k-biru">
            <div class="label">Mahasiswa PA</div>
            <div class="angka">{{ $mahasiswaPa }}</div>
        </div>
        <div class="kartu k-oranye">
            <div class="label">Pengajuan Menunggu</div>
            <div class="angka">{{ $pengajuanMenunggu }}</div>
        </div>
        <div class="kartu k-biru">
            <div class="label">Mahasiswa Bimbingan Magang</div>
            <div class="angka">{{ $mahasiswaBimbingan }}</div>
        </div>
        <div class="kartu k-oranye">
            <div class="label">Bimbingan Menunggu Tanggapan</div>
            <div class="angka">{{ $bimbinganMenunggu }}</div>
        </div>
        <div class="kartu k-merah">
            <div class="label">Belum Dinilai</div>
            <div class="angka">{{ $belumDinilai }}</div>
        </div>
        <div class="kartu k-oranye">
            <div class="label">Laporan Menunggu Diperiksa</div>
            <div class="angka">{{ $laporanMenunggu }}</div>
        </div>
    </div>

    <div class="panel">
        <h3>Pengajuan Terbaru</h3>
        @if ($pengajuanTerbaru->isEmpty())
            <div class="kosong">Belum ada pengajuan dari mahasiswa PA Anda.</div>
        @else
            <table>
                <tr><th>Nama</th><th>NIM</th><th>Tempat Magang</th><th>Status</th><th></th></tr>
                @foreach ($pengajuanTerbaru as $p)
                    <tr>
                        <td>{{ $p->pendaftaran?->mahasiswa?->pengguna?->nama_lengkap }}</td>
                        <td>{{ $p->pendaftaran?->mahasiswa?->nim }}</td>
                        <td>{{ $p->tempat?->nama_instansi }}</td>
                        <td>@include('partials.badge', ['status' => $p->status_pengajuan])</td>
                        <td><a class="btn btn-kecil" href="{{ route('dosen.verifikasi.show', $p->id_pengajuan) }}">Detail</a></td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="panel">
        <h3>Notifikasi</h3>
        @forelse ($notifikasi as $n)
            <div class="notif {{ $n->sudah_dibaca ? '' : 'baru' }}"><span class="titik"></span><div>
                <strong>{{ $n->judul }}</strong>
                @unless ($n->sudah_dibaca) <span class="badge b-biru">Baru</span> @endunless
                @if ($n->id_referensi && in_array($n->jenis_notifikasi, ['Pengajuan Baru', 'Pengajuan Direvisi']))
                    <a href="{{ route('dosen.verifikasi.show', $n->id_referensi) }}" style="font-size:12.5px;margin-left:6px">Buka</a>
                @elseif ($n->id_referensi && $n->jenis_notifikasi === 'Bimbingan Baru')
                    <a href="{{ route('dosen.bimbingan.show', $n->id_referensi) }}" style="font-size:12.5px;margin-left:6px">Buka</a>
                @elseif ($n->id_referensi && $n->jenis_notifikasi === 'Laporan Baru')
                    <a href="{{ route('dosen.laporan.show', $n->id_referensi) }}" style="font-size:12.5px;margin-left:6px">Buka</a>
                @endif
                <div class="isi">{{ $n->pesan }}</div>
            </div></div>
        @empty
            <div class="kosong">Belum ada notifikasi.</div>
        @endforelse
    </div>
@endsection
