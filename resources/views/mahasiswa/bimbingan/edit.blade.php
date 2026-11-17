@extends('layouts.app')
@section('title', 'Ubah Bimbingan')
@section('judul', 'Ubah Catatan Bimbingan')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    <form method="POST" action="{{ route('mahasiswa.bimbingan.update', $b->id_bimbingan) }}">
        @csrf @method('PUT')
        <div class="panel">
            <div class="baris">
                <div class="grup">
                    <label for="tanggal_bimbingan">Tanggal</label>
                    <input class="input" type="date" id="tanggal_bimbingan" name="tanggal_bimbingan"
                           value="{{ old('tanggal_bimbingan', $b->tanggal_bimbingan?->format('Y-m-d')) }}" required>
                    @error('tanggal_bimbingan') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="topik">Topik</label>
                    <input class="input" id="topik" name="topik" maxlength="150" value="{{ old('topik', $b->topik) }}" required>
                    @error('topik') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="uraian_kegiatan">Uraian Kegiatan</label>
                <textarea class="input" id="uraian_kegiatan" name="uraian_kegiatan" style="min-height:140px" required>{{ old('uraian_kegiatan', $b->uraian_kegiatan) }}</textarea>
                @error('uraian_kegiatan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-abu" href="{{ route('mahasiswa.bimbingan.index') }}">Batal</a>
    </form>
@endsection
