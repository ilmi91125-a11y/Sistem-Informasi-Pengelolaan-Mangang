@extends('layouts.app')
@section('title', 'Tempat Magang')
@section('judul', $t ? 'Ubah Tempat Magang' : 'Tambah Tempat Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <form method="POST" action="{{ $t ? route('admin.tempat.update', $t->id_tempat) : route('admin.tempat.store') }}">
        @csrf
        @if ($t) @method('PUT') @endif

        <div class="panel">
            <div class="grup">
                <label for="nama_instansi">Nama Instansi</label>
                <input class="input" id="nama_instansi" name="nama_instansi" maxlength="150" value="{{ old('nama_instansi', $t?->nama_instansi) }}" required>
                @error('nama_instansi') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="alamat">Alamat</label>
                <textarea class="input" id="alamat" name="alamat" required>{{ old('alamat', $t?->alamat) }}</textarea>
                @error('alamat') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="baris">
                <div class="grup">
                    <label for="bidang_usaha">Bidang Usaha</label>
                    <input class="input" id="bidang_usaha" name="bidang_usaha" maxlength="100" value="{{ old('bidang_usaha', $t?->bidang_usaha) }}">
                    @error('bidang_usaha') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="kontak">Kontak</label>
                    <input class="input" id="kontak" name="kontak" maxlength="100" value="{{ old('kontak', $t?->kontak) }}">
                    @error('kontak') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="input" id="deskripsi" name="deskripsi">{{ old('deskripsi', $t?->deskripsi) }}</textarea>
            </div>
            <label style="font-weight:400">
                <input type="checkbox" name="status_aktif" value="1" @checked(old('status_aktif', $t ? $t->status_aktif : true))>
                Aktif (tampil di pilihan mahasiswa)
            </label>
        </div>

        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-abu" href="{{ route('admin.tempat.index') }}">Batal</a>
    </form>
@endsection
