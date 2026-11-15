@extends('layouts.app')
@section('title', 'Informasi Magang')
@section('judul', 'Informasi dan Ketentuan Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @forelse ($ketentuan as $k)
        <div class="panel">
            <h3>{{ $k->judul }}</h3>
            <div style="white-space:pre-line;font-size:14px;line-height:1.6">{{ $k->isi_ketentuan }}</div>
            @if ($k->url_berkas)
                @php
                    $link = \Illuminate\Support\Str::startsWith($k->url_berkas, ['http://', 'https://'])
                        ? $k->url_berkas : \Illuminate\Support\Facades\Storage::disk('public')->url($k->url_berkas);
                @endphp
                <p><a class="btn btn-kecil" href="{{ $link }}" target="_blank">Unduh Berkas</a></p>
            @endif
            <small style="color:#6b7280">Diperbarui {{ $k->created_at?->format('d/m/Y') }}</small>
        </div>
    @empty
        <div class="panel"><div class="kosong">Belum ada ketentuan magang yang dipublikasikan.</div></div>
    @endforelse
@endsection
