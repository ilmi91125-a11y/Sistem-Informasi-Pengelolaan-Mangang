@extends('layouts.app')
@section('title', 'Ketentuan Magang')
@section('judul', $k ? 'Ubah Ketentuan' : 'Tambah Ketentuan')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $k ? route('admin.ketentuan.update', $k->id_ketentuan) : route('admin.ketentuan.store') }}">
        @csrf
        @if ($k) @method('PUT') @endif

        <div class="panel">
            <div class="grup">
                <label for="judul">Judul</label>
                <input class="input" id="judul" name="judul" maxlength="150" value="{{ old('judul', $k?->judul) }}" required>
                @error('judul') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="isi_ketentuan">Isi Ketentuan</label>
                <textarea class="input" id="isi_ketentuan" name="isi_ketentuan" style="min-height:200px" required>{{ old('isi_ketentuan', $k?->isi_ketentuan) }}</textarea>
                @error('isi_ketentuan') <div class="galat-field">{{ $message }}</div> @enderror
                <small>Enter (baris baru) akan ikut tampil di halaman mahasiswa.</small>
            </div>
            <div class="grup">
                <label for="berkas">Berkas Pendukung (opsional, PDF/DOC/DOCX maks. 5 MB)</label>
                <input class="input" type="file" id="berkas" name="berkas" accept=".pdf,.doc,.docx">
                @error('berkas') <div class="galat-field">{{ $message }}</div> @enderror
                @if ($k?->url_berkas)
                    <small>Berkas saat ini tersimpan. Mengunggah berkas baru akan menggantinya.</small>
                    <label style="font-weight:400;margin-top:6px"><input type="checkbox" name="hapus_berkas" value="1"> Hapus berkas saat ini</label>
                @endif
            </div>
        </div>

        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-abu" href="{{ route('admin.ketentuan.index') }}">Batal</a>
    </form>
@endsection
