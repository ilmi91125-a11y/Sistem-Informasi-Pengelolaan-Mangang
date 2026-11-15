@extends('layouts.app')
@section('title', 'Ketentuan Magang')
@section('judul', 'Ketentuan Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    <p><a class="btn" href="{{ route('admin.ketentuan.create') }}">+ Tambah Ketentuan</a></p>

    <div class="panel">
        @if ($daftar->isEmpty())
            <div class="kosong">Belum ada ketentuan.</div>
        @else
            <div style="overflow-x:auto">
            <table>
                <tr><th>Judul</th><th>Berkas</th><th>Dibuat</th><th style="width:170px"></th></tr>
                @foreach ($daftar as $k)
                    <tr>
                        <td>{{ $k->judul }}</td>
                        <td>{{ $k->url_berkas ? 'Ada' : '-' }}</td>
                        <td>{{ $k->created_at?->format('d/m/Y') }}</td>
                        <td style="white-space:nowrap">
                            <a class="btn btn-kecil" href="{{ route('admin.ketentuan.edit', $k->id_ketentuan) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.ketentuan.destroy', $k->id_ketentuan) }}" style="display:inline"
                                  onsubmit="return confirm('Hapus ketentuan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-kecil" style="background:#dc2626" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
