@extends('layouts.app')
@section('title', 'Pendaftaran Magang')
@section('judul', 'Pendaftaran Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    <div class="panel">
        <h3>Daftar Magang Baru</h3>
        <form method="POST" action="{{ route('mahasiswa.pendaftaran.store') }}">
            @csrf
            <div class="grup">
                <label for="periode_magang">Periode Magang</label>
                <input class="input" id="periode_magang" name="periode_magang" maxlength="30"
                       value="{{ old('periode_magang') }}" placeholder="Contoh: Genap 2026/2027" required>
                @error('periode_magang') <div class="galat-field">{{ $message }}</div> @enderror
                <small>Satu mahasiswa hanya dapat mendaftar satu kali per periode.</small>
            </div>
            <button class="btn" type="submit">Daftar</button>
        </form>
    </div>

    <div class="panel">
        <h3>Riwayat Pendaftaran</h3>
        @if ($daftar->isEmpty())
            <div class="kosong">Belum ada pendaftaran.</div>
        @else
            <table>
                <tr><th>Periode</th><th>Tanggal Daftar</th><th>Status</th></tr>
                @foreach ($daftar as $d)
                    <tr>
                        <td>{{ $d->periode_magang }}</td>
                        <td>{{ $d->tanggal_daftar->format('d/m/Y') }}</td>
                        <td>@include('partials.badge', ['status' => $d->status_pendaftaran])</td>
                    </tr>
                @endforeach
            </table>
            <p style="margin-top:14px"><a class="btn btn-kecil" href="{{ route('mahasiswa.pengajuan.create') }}">Ajukan Tempat Magang</a></p>
        @endif
    </div>
@endsection
