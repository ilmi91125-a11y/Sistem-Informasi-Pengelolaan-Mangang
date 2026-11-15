@extends('layouts.app')
@section('title', 'Perbaiki Pengajuan')
@section('judul', 'Perbaiki Pengajuan')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @if ($catatan)
        <div class="info"><strong>Catatan dosen PA:</strong><br>{{ $catatan->catatan }}</div>
    @endif

    <form method="POST" action="{{ route('mahasiswa.pengajuan.update', $pengajuan->id_pengajuan) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="panel">
            <h3>Rencana Magang - {{ $pengajuan->tempat?->nama_instansi }}</h3>
            <div class="grup">
                <label for="rencana_kegiatan">Rencana Kegiatan</label>
                <textarea class="input" id="rencana_kegiatan" name="rencana_kegiatan" required>{{ old('rencana_kegiatan', $pengajuan->rencana_kegiatan) }}</textarea>
                @error('rencana_kegiatan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="baris">
                <div class="grup">
                    <label for="rencana_mulai">Tanggal Mulai</label>
                    <input class="input" type="date" id="rencana_mulai" name="rencana_mulai" value="{{ old('rencana_mulai', $pengajuan->rencana_mulai?->format('Y-m-d')) }}" required>
                    @error('rencana_mulai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="rencana_selesai">Tanggal Selesai</label>
                    <input class="input" type="date" id="rencana_selesai" name="rencana_selesai" value="{{ old('rencana_selesai', $pengajuan->rencana_selesai?->format('Y-m-d')) }}" required>
                    @error('rencana_selesai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="panel">
            <h3>Unggah Berkas Pengganti (opsional)</h3>
            <div class="grup">
                <label for="proposal">Proposal</label>
                <input class="input" type="file" id="proposal" name="proposal" accept=".pdf,.doc,.docx">
                @error('proposal') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="surat_penerimaan">Surat Penerimaan</label>
                <input class="input" type="file" id="surat_penerimaan" name="surat_penerimaan" accept=".pdf,.doc,.docx">
                @error('surat_penerimaan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="berkas_lain">Berkas Lainnya</label>
                <input class="input" type="file" id="berkas_lain" name="berkas_lain" accept=".pdf,.doc,.docx">
                @error('berkas_lain') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <small style="color:#6b7280">Berkas lama tetap tersimpan; berkas baru ditambahkan.</small>
        </div>

        <button class="btn" type="submit">Kirim Ulang</button>
        <a class="btn btn-abu" href="{{ route('mahasiswa.pengajuan.show', $pengajuan->id_pengajuan) }}">Batal</a>
    </form>
@endsection
