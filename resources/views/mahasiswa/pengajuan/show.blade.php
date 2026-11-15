@extends('layouts.app')
@section('title', 'Detail Pengajuan')
@section('judul', 'Detail Pengajuan')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    <div class="panel">
        <h3>Ringkasan</h3>
        <table class="rincian">
            <tr><th>Status</th><td>@include('partials.badge', ['status' => $pengajuan->status_pengajuan])</td></tr>
            <tr><th>Periode</th><td>{{ $pengajuan->pendaftaran?->periode_magang }}</td></tr>
            <tr><th>Instansi</th><td>{{ $pengajuan->tempat?->nama_instansi }}</td></tr>
            <tr><th>Alamat</th><td>{{ $pengajuan->tempat?->alamat }}</td></tr>
            <tr><th>Rencana Kegiatan</th><td style="white-space:pre-line">{{ $pengajuan->rencana_kegiatan }}</td></tr>
            <tr><th>Rencana Pelaksanaan</th><td>{{ $pengajuan->rencana_mulai?->format('d/m/Y') }} s.d. {{ $pengajuan->rencana_selesai?->format('d/m/Y') }}</td></tr>
            <tr><th>Tanggal Pengajuan</th><td>{{ $pengajuan->tanggal_pengajuan?->format('d/m/Y H:i') }}</td></tr>
            @if ($pengajuan->surat)
                <tr><th>Surat Magang</th><td>@include('partials.badge', ['status' => $pengajuan->surat->status_surat])</td></tr>
            @endif
        </table>
        @if ($pengajuan->status_pengajuan === 'Revisi')
            <p style="margin-top:14px"><a class="btn" href="{{ route('mahasiswa.pengajuan.edit', $pengajuan->id_pengajuan) }}">Perbaiki Pengajuan</a></p>
        @endif
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

    <div class="panel">
        <h3>Riwayat Verifikasi Dosen PA</h3>
        @forelse ($riwayat as $v)
            <div style="padding:8px 0;border-bottom:1px solid #e5e7eb;font-size:14px">
                @include('partials.badge', ['status' => $v->keputusan])
                <span style="color:#6b7280">{{ $v->tanggal_verifikasi?->format('d/m/Y H:i') }} - {{ $v->dosen?->pengguna?->nama_lengkap }}</span>
                @if ($v->catatan)
                    <div style="margin-top:6px;white-space:pre-line">{{ $v->catatan }}</div>
                @endif
            </div>
        @empty
            <div class="kosong">Belum diverifikasi.</div>
        @endforelse
    </div>

    <a class="btn btn-abu" href="{{ route('mahasiswa.pengajuan.index') }}">Kembali</a>
@endsection
