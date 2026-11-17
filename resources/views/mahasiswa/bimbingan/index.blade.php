@extends('layouts.app')
@section('title', 'Bimbingan')
@section('judul', 'Bimbingan Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @if (! $penempatan)
        <div class="info">Anda belum memiliki penempatan magang. Bimbingan dapat diisi setelah Tim Akademik mencatat penempatan Anda.</div>
    @else
        <div class="panel">
            <h3>Penempatan</h3>
            <table class="rincian">
                <tr><th>Tempat</th><td>{{ $penempatan->tempat?->nama_instansi }}</td></tr>
                <tr><th>Periode</th><td>{{ $penempatan->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $penempatan->tanggal_selesai?->format('d/m/Y') }}</td></tr>
                <tr><th>Dosen Pembimbing</th>
                    <td>{{ $penempatan->dosenPembimbing?->pengguna?->nama_lengkap ?? 'Belum ditetapkan' }}</td></tr>
                <tr><th>Status</th><td>@include('partials.badge', ['status' => $penempatan->status_magang])</td></tr>
            </table>
        </div>

        @if ($penempatan->status_magang === 'Berjalan')
            <div class="panel">
                <h3>Tambah Catatan Bimbingan</h3>
                <form method="POST" action="{{ route('mahasiswa.bimbingan.store') }}">
                    @csrf
                    <div class="baris">
                        <div class="grup">
                            <label for="tanggal_bimbingan">Tanggal</label>
                            <input class="input" type="date" id="tanggal_bimbingan" name="tanggal_bimbingan"
                                   value="{{ old('tanggal_bimbingan', now()->toDateString()) }}" required>
                            @error('tanggal_bimbingan') <div class="galat-field">{{ $message }}</div> @enderror
                        </div>
                        <div class="grup">
                            <label for="topik">Topik</label>
                            <input class="input" id="topik" name="topik" maxlength="150" value="{{ old('topik') }}" required>
                            @error('topik') <div class="galat-field">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="grup">
                        <label for="uraian_kegiatan">Uraian Kegiatan</label>
                        <textarea class="input" id="uraian_kegiatan" name="uraian_kegiatan" style="min-height:120px" required>{{ old('uraian_kegiatan') }}</textarea>
                        @error('uraian_kegiatan') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn" type="submit">Simpan</button>
                </form>
            </div>
        @else
            <div class="info">Magang Anda sudah berstatus Selesai, sehingga bimbingan baru tidak dapat ditambahkan.</div>
        @endif

        <h3 style="margin:24px 0 12px">Riwayat Bimbingan ({{ $daftar->count() }})</h3>
        @forelse ($daftar as $b)
            <div class="panel">
                <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">
                    <div><strong>{{ $b->topik }}</strong>
                        <span style="color:#6b7280;font-size:13px"> - {{ $b->tanggal_bimbingan?->format('d/m/Y') }}</span></div>
                    <div>@include('partials.badge', ['status' => $b->status_bimbingan])</div>
                </div>
                <div style="white-space:pre-line;font-size:14px;margin-top:8px">{{ $b->uraian_kegiatan }}</div>

                @if ($b->tanggapan_dosen)
                    <div style="margin-top:12px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:14px">
                        <strong>Tanggapan dosen</strong>
                        <span style="color:#6b7280;font-size:13px">({{ $b->tanggal_tanggapan?->format('d/m/Y H:i') }})</span>
                        <div style="white-space:pre-line;margin-top:4px">{{ $b->tanggapan_dosen }}</div>
                    </div>
                @endif

                @if ($b->status_bimbingan === 'Menunggu')
                    <div style="margin-top:12px;display:flex;gap:8px">
                        <a class="btn btn-kecil btn-abu" href="{{ route('mahasiswa.bimbingan.edit', $b->id_bimbingan) }}">Ubah</a>
                        <form method="POST" action="{{ route('mahasiswa.bimbingan.destroy', $b->id_bimbingan) }}"
                              onsubmit="return confirm('Hapus catatan bimbingan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-kecil" style="background:#dc2626" type="submit">Hapus</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="panel"><div class="kosong">Belum ada catatan bimbingan.</div></div>
        @endforelse
    @endif
@endsection
