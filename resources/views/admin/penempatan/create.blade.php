@extends('layouts.app')
@section('title', 'Catat Penempatan')
@section('judul', 'Catat Penempatan Magang')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    @if ($kandidat->isEmpty())
        <div class="info">Tidak ada pengajuan disetujui yang belum ditempatkan.</div>
        <a class="btn btn-abu" href="{{ route('admin.penempatan.index') }}">Kembali</a>
    @else
    <form method="POST" action="{{ route('admin.penempatan.store') }}">
        @csrf
        <div class="panel">
            <div class="grup">
                <label for="id_pengajuan">Pengajuan yang Disetujui</label>
                <select class="input" id="id_pengajuan" name="id_pengajuan" onchange="isiTanggal()" required>
                    <option value="">-- Pilih mahasiswa dan tempat --</option>
                    @foreach ($kandidat as $k)
                        <option value="{{ $k->id_pengajuan }}"
                                data-mulai="{{ $k->rencana_mulai?->format('Y-m-d') }}"
                                data-selesai="{{ $k->rencana_selesai?->format('Y-m-d') }}"
                                @selected(old('id_pengajuan') === $k->id_pengajuan)>
                            {{ $k->pendaftaran?->mahasiswa?->pengguna?->nama_lengkap }} ({{ $k->pendaftaran?->mahasiswa?->nim }})
                            - {{ $k->tempat?->nama_instansi }}{{ $k->tempat?->status_aktif ? '' : ' [tempat belum aktif]' }}
                        </option>
                    @endforeach
                </select>
                @error('id_pengajuan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="baris">
                <div class="grup">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input class="input" type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                    @error('tanggal_mulai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input class="input" type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                    @error('tanggal_selesai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="id_dosen_pembimbing">Dosen Pembimbing (bisa diisi nanti)</label>
                <select class="input" id="id_dosen_pembimbing" name="id_dosen_pembimbing">
                    <option value="">-- Belum ditetapkan --</option>
                    @foreach ($dosenList as $d)
                        <option value="{{ $d->id_dosen }}" @selected(old('id_dosen_pembimbing') === $d->id_dosen)>{{ $d->pengguna?->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('id_dosen_pembimbing') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <small style="color:#6b7280">Tanggal terisi otomatis dari rencana mahasiswa dan boleh diubah. Tempat harus berstatus aktif.</small>
        </div>

        <button class="btn" type="submit">Simpan Penempatan</button>
        <a class="btn btn-abu" href="{{ route('admin.penempatan.index') }}">Batal</a>
    </form>
    <script>
        function isiTanggal() {
            var o = document.getElementById('id_pengajuan').selectedOptions[0];
            if (!o || !o.value) return;
            document.getElementById('tanggal_mulai').value = o.dataset.mulai || '';
            document.getElementById('tanggal_selesai').value = o.dataset.selesai || '';
        }
    </script>
    @endif
@endsection
