@extends('layouts.app')
@section('title', 'Ubah Penempatan')
@section('judul', 'Ubah Penempatan Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <div class="panel">
        <table class="rincian">
            <tr><th>Mahasiswa</th><td>{{ $p->mahasiswa?->pengguna?->nama_lengkap }} ({{ $p->mahasiswa?->nim }})</td></tr>
            <tr><th>Tempat</th><td>{{ $p->tempat?->nama_instansi }}</td></tr>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.penempatan.update', $p->id_penempatan) }}">
        @csrf @method('PUT')
        <div class="panel">
            <div class="baris">
                <div class="grup">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input class="input" type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $p->tanggal_mulai?->format('Y-m-d')) }}" required>
                    @error('tanggal_mulai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input class="input" type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $p->tanggal_selesai?->format('Y-m-d')) }}" required>
                    @error('tanggal_selesai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="id_dosen_pembimbing">Dosen Pembimbing</label>
                <select class="input" id="id_dosen_pembimbing" name="id_dosen_pembimbing">
                    <option value="">-- Belum ditetapkan --</option>
                    @foreach ($dosenList as $d)
                        <option value="{{ $d->id_dosen }}" @selected(old('id_dosen_pembimbing', $p->id_dosen_pembimbing) === $d->id_dosen)>{{ $d->pengguna?->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('id_dosen_pembimbing') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="status_magang">Status Magang</label>
                <select class="input" id="status_magang" name="status_magang">
                    @foreach (['Berjalan', 'Selesai'] as $s)
                        <option value="{{ $s }}" @selected(old('status_magang', $p->status_magang) === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-abu" href="{{ route('admin.penempatan.index') }}">Batal</a>
    </form>
@endsection
