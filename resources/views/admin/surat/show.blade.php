@extends('layouts.app')
@section('title', 'Proses Surat')
@section('judul', 'Proses Surat Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    @php
        $p = $surat->pengajuan;
        $mhs = $p?->pendaftaran?->mahasiswa;
    @endphp

    <div class="panel">
        <h3>Pemohon dan Tujuan</h3>
        <table class="rincian">
            <tr><th>Mahasiswa</th><td>{{ $mhs?->pengguna?->nama_lengkap }} ({{ $mhs?->nim }})</td></tr>
            <tr><th>Program Studi</th><td>{{ $mhs?->program_studi }}</td></tr>
            <tr><th>Instansi</th>
                <td>{{ $p?->tempat?->nama_instansi }}
                    @if (! $p?->tempat?->status_aktif) <span class="badge b-kuning">Tempat belum aktif</span> @endif
                </td></tr>
            <tr><th>Alamat</th><td>{{ $p?->tempat?->alamat }}</td></tr>
            <tr><th>Rencana Pelaksanaan</th><td>{{ $p?->rencana_mulai?->format('d/m/Y') }} s.d. {{ $p?->rencana_selesai?->format('d/m/Y') }}</td></tr>
            <tr><th>Diminta</th><td>{{ $surat->tanggal_pengajuan?->format('d/m/Y H:i') }}</td></tr>
            <tr><th>Status Surat</th><td>@include('partials.badge', ['status' => $surat->status_surat])</td></tr>
        </table>
    </div>

    <div class="panel">
        <h3>Berkas Pengajuan</h3>
        @forelse ($p?->berkas ?? [] as $b)
            <div style="padding:6px 0;border-bottom:1px solid #e5e7eb;font-size:14px">
                <strong>{{ $b->jenis_berkas }}</strong> -
                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($b->url_berkas) }}" target="_blank">{{ $b->nama_berkas }}</a>
                <span style="color:#6b7280">({{ $b->tanggal_unggah?->format('d/m/Y') }})</span>
            </div>
        @empty
            <div class="kosong">Tidak ada berkas.</div>
        @endforelse
    </div>

    @if ($surat->status_surat === 'Selesai')
        <div class="panel">
            <h3>Hasil</h3>
            <table class="rincian">
                <tr><th>Nomor Surat</th><td>{{ $surat->nomor_surat }}</td></tr>
                <tr><th>Selesai</th><td>{{ $surat->tanggal_selesai?->format('d/m/Y') }}</td></tr>
                <tr><th>Pemroses</th><td>{{ $surat->pemroses?->nama_lengkap }}</td></tr>
                <tr><th>Berkas</th><td>
                    @if ($surat->url_berkas)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($surat->url_berkas) }}" target="_blank">Unduh surat</a>
                    @endif
                </td></tr>
            </table>
        </div>
    @else
        <div class="panel">
            <h3>Proses Surat</h3>
            <form method="POST" action="{{ route('admin.surat.update', $surat->id_surat) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="opsi" style="flex-wrap:wrap">
                    @foreach (['Diproses' => 'Diproses', 'Perlu Dilengkapi' => 'Perlu Dilengkapi', 'Selesai' => 'Selesai'] as $nilai => $label)
                        <label><input type="radio" name="status_surat" value="{{ $nilai }}"
                            @checked(old('status_surat', $surat->status_surat) === $nilai) onchange="ubahStatus()"> {{ $label }}</label>
                    @endforeach
                </div>
                @error('status_surat') <div class="galat-field">{{ $message }}</div> @enderror

                <div id="blok-selesai">
                    <div class="grup">
                        <label for="nomor_surat">Nomor Surat</label>
                        <input class="input" id="nomor_surat" name="nomor_surat" maxlength="50" value="{{ old('nomor_surat', $surat->nomor_surat) }}">
                        @error('nomor_surat') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                    <div class="grup">
                        <label for="file_surat">Berkas Surat (PDF/DOC/DOCX maks. 5 MB)</label>
                        <input class="input" type="file" id="file_surat" name="file_surat" accept=".pdf,.doc,.docx">
                        @error('file_surat') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div id="blok-catatan" class="grup">
                    <label for="catatan">Catatan untuk Mahasiswa</label>
                    <textarea class="input" id="catatan" name="catatan" placeholder="Jelaskan apa yang perlu dilengkapi">{{ old('catatan', $surat->catatan) }}</textarea>
                    @error('catatan') <div class="galat-field">{{ $message }}</div> @enderror
                </div>

                <button class="btn" type="submit" onclick="return confirm('Simpan status surat ini?')">Simpan</button>
                <a class="btn btn-abu" href="{{ route('admin.surat.index') }}">Kembali</a>
            </form>
        </div>
        <script>
            function ubahStatus() {
                var s = document.querySelector('input[name="status_surat"]:checked').value;
                document.getElementById('blok-selesai').style.display = s === 'Selesai' ? 'block' : 'none';
                document.getElementById('blok-catatan').style.display = s === 'Perlu Dilengkapi' ? 'block' : 'none';
            }
            ubahStatus();
        </script>
    @endif
@endsection
