@extends('layouts.app')
@section('title', 'Surat Magang')
@section('judul', 'Surat Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @forelse ($daftar as $p)
        @php $surat = $p->surat; @endphp
        <div class="panel">
            <h3>{{ $p->tempat?->nama_instansi }}</h3>
            <table class="rincian">
                <tr><th>Periode</th><td>{{ $p->pendaftaran?->periode_magang }}</td></tr>
                <tr><th>Status Surat</th>
                    <td>@if ($surat) @include('partials.badge', ['status' => $surat->status_surat])
                        @else <span class="badge b-abu">Belum diminta</span> @endif</td></tr>
                @if ($surat?->nomor_surat)
                    <tr><th>Nomor Surat</th><td>{{ $surat->nomor_surat }}</td></tr>
                @endif
                @if ($surat?->status_surat === 'Perlu Dilengkapi' && $surat->catatan)
                    <tr><th>Catatan Akademik</th><td style="white-space:pre-line">{{ $surat->catatan }}</td></tr>
                @endif
            </table>

            @if (! $surat)
                <form method="POST" action="{{ route('mahasiswa.surat.store', $p->id_pengajuan) }}" style="margin-top:14px"
                      onsubmit="return confirm('Ajukan permohonan surat magang?')">
                    @csrf
                    <button class="btn" type="submit">Ajukan Surat Magang</button>
                </form>
            @elseif ($surat->status_surat === 'Perlu Dilengkapi')
                <form method="POST" action="{{ route('mahasiswa.surat.lengkapi', $p->id_pengajuan) }}" enctype="multipart/form-data" style="margin-top:14px">
                    @csrf
                    <div class="grup">
                        <label>Unggah Kelengkapan (PDF/DOC/DOCX, maks. 5 MB per berkas)</label>
                        <input class="input" type="file" name="berkas_tambahan[]" multiple accept=".pdf,.doc,.docx" required>
                        @error('berkas_tambahan') <div class="galat-field">{{ $message }}</div> @enderror
                        @error('berkas_tambahan.*') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn" type="submit">Kirim Kelengkapan</button>
                </form>
            @elseif ($surat->status_surat === 'Selesai' && $surat->url_berkas)
                <p style="margin-top:14px"><a class="btn" target="_blank"
                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($surat->url_berkas) }}">Unduh Surat</a></p>
            @endif
        </div>
    @empty
        <div class="panel">
            <div class="kosong">Belum ada pengajuan yang disetujui dosen PA. Surat dapat diminta setelah pengajuan Anda disetujui.</div>
        </div>
    @endforelse
@endsection
