@extends('layouts.app')
@section('title', 'Laporan')
@section('judul', 'Laporan Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @if (! $penempatan)
        <div class="info">Anda belum memiliki penempatan magang. Laporan dapat diunggah setelah Tim Akademik mencatat penempatan Anda.</div>
    @else
        <div class="panel">
            <h3>Penempatan</h3>
            <table class="rincian">
                <tr><th>Tempat</th><td>{{ $penempatan->tempat?->nama_instansi }}</td></tr>
                <tr><th>Periode</th><td>{{ $penempatan->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $penempatan->tanggal_selesai?->format('d/m/Y') }}</td></tr>
                <tr><th>Dosen Pembimbing</th><td>{{ $penempatan->dosenPembimbing?->pengguna?->nama_lengkap ?? 'Belum ditetapkan' }}</td></tr>
            </table>
        </div>

        @if ($bolehUnggah)
            <div class="panel">
                <h3>Unggah Laporan</h3>
                <form method="POST" action="{{ route('mahasiswa.laporan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grup">
                        <label for="judul_laporan">Judul Laporan</label>
                        <input class="input" id="judul_laporan" name="judul_laporan" maxlength="200" value="{{ old('judul_laporan') }}" required>
                        @error('judul_laporan') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                    <div class="grup">
                        <label for="berkas">Berkas (PDF/DOC/DOCX, maks. 10 MB)</label>
                        <input class="input" type="file" id="berkas" name="berkas" accept=".pdf,.doc,.docx" required>
                        @error('berkas') <div class="galat-field">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn" type="submit">Unggah</button>
                </form>
            </div>
        @else
            <div class="info">Laporan Anda sedang menunggu pemeriksaan atau sudah diterima, jadi unggahan baru tidak dibuka. Jika perlu revisi, form unggah akan muncul kembali.</div>
        @endif

        <h3 style="margin:24px 0 12px">Riwayat Laporan ({{ $daftar->count() }})</h3>
        @forelse ($daftar as $l)
            <div class="panel">
                <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">
                    <div><strong>{{ $l->judul_laporan }}</strong>
                        <span style="color:#6b7280;font-size:13px"> - diunggah {{ $l->tanggal_unggah?->format('d/m/Y H:i') }}</span></div>
                    <div>@include('partials.badge', ['status' => $l->status_laporan])</div>
                </div>
                <p style="margin:10px 0 0">
                    <a class="btn btn-kecil btn-abu" target="_blank"
                       href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($l->url_berkas) }}">Buka Berkas</a>
                </p>

                @if ($l->catatan_dosen)
                    <div style="margin-top:12px;padding:10px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;font-size:14px">
                        <strong>Catatan dosen</strong>
                        <div style="white-space:pre-line;margin-top:4px">{{ $l->catatan_dosen }}</div>
                    </div>
                @endif

                @if ($l->status_laporan === 'Diunggah')
                    <form method="POST" action="{{ route('mahasiswa.laporan.destroy', $l->id_laporan) }}" style="margin-top:12px"
                          onsubmit="return confirm('Hapus laporan ini untuk mengunggah ulang?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-kecil" style="background:#dc2626" type="submit">Hapus</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="panel"><div class="kosong">Belum ada laporan.</div></div>
        @endforelse
    @endif
@endsection
