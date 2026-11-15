@extends('layouts.app')
@section('title', 'Ajukan Tempat Magang')
@section('judul', 'Ajukan Tempat Magang')
@section('menu') @include('partials.menu_mahasiswa') @endsection

@section('isi')
    @if ($pendaftaranTersedia->isEmpty())
        <div class="info">
            Tidak ada pendaftaran yang bisa diajukan. Anda perlu
            <a href="{{ route('mahasiswa.pendaftaran') }}">mendaftar magang</a> terlebih dahulu,
            atau pendaftaran Anda sudah memiliki pengajuan yang masih aktif.
        </div>
    @else
    <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="panel">
            <h3>1. Pendaftaran</h3>
            <div class="grup">
                <label for="id_pendaftaran">Periode</label>
                <select class="input" id="id_pendaftaran" name="id_pendaftaran" required>
                    @foreach ($pendaftaranTersedia as $d)
                        <option value="{{ $d->id_pendaftaran }}" @selected(old('id_pendaftaran') === $d->id_pendaftaran)>{{ $d->periode_magang }}</option>
                    @endforeach
                </select>
                @error('id_pendaftaran') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="panel">
            <h3>2. Tempat Magang</h3>
            @php $mode = old('tempat_mode', 'pilih'); @endphp
            <div class="opsi">
                <label><input type="radio" name="tempat_mode" value="pilih" @checked($mode === 'pilih') onchange="ubahMode()"> Pilih dari daftar</label>
                <label><input type="radio" name="tempat_mode" value="baru" @checked($mode === 'baru') onchange="ubahMode()"> Usulkan tempat baru</label>
            </div>

            <div id="blok-pilih">
                <div class="grup">
                    <label for="id_tempat">Instansi</label>
                    <select class="input" id="id_tempat" name="id_tempat">
                        <option value="">-- Pilih instansi --</option>
                        @foreach ($tempatList as $t)
                            <option value="{{ $t->id_tempat }}" @selected(old('id_tempat') === $t->id_tempat)>{{ $t->nama_instansi }}{{ $t->bidang_usaha ? ' - ' . $t->bidang_usaha : '' }}</option>
                        @endforeach
                    </select>
                    @error('id_tempat') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>

            <div id="blok-baru">
                <div class="grup">
                    <label for="nama_instansi">Nama Instansi</label>
                    <input class="input" id="nama_instansi" name="nama_instansi" value="{{ old('nama_instansi') }}" maxlength="150">
                    @error('nama_instansi') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="alamat">Alamat</label>
                    <textarea class="input" id="alamat" name="alamat">{{ old('alamat') }}</textarea>
                    @error('alamat') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="baris">
                    <div class="grup">
                        <label for="bidang_usaha">Bidang Usaha</label>
                        <input class="input" id="bidang_usaha" name="bidang_usaha" value="{{ old('bidang_usaha') }}" maxlength="100">
                    </div>
                    <div class="grup">
                        <label for="kontak">Kontak</label>
                        <input class="input" id="kontak" name="kontak" value="{{ old('kontak') }}" maxlength="100">
                    </div>
                </div>
                <small style="color:#6b7280">Tempat usulan akan divalidasi Tim Akademik.</small>
            </div>
        </div>

        <div class="panel">
            <h3>3. Rencana Magang</h3>
            <div class="grup">
                <label for="rencana_kegiatan">Rencana Kegiatan</label>
                <textarea class="input" id="rencana_kegiatan" name="rencana_kegiatan" required>{{ old('rencana_kegiatan') }}</textarea>
                @error('rencana_kegiatan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="baris">
                <div class="grup">
                    <label for="rencana_mulai">Tanggal Mulai</label>
                    <input class="input" type="date" id="rencana_mulai" name="rencana_mulai" value="{{ old('rencana_mulai') }}" required>
                    @error('rencana_mulai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="rencana_selesai">Tanggal Selesai</label>
                    <input class="input" type="date" id="rencana_selesai" name="rencana_selesai" value="{{ old('rencana_selesai') }}" required>
                    @error('rencana_selesai') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="panel">
            <h3>4. Berkas (PDF/DOC/DOCX, maks. 5 MB)</h3>
            <div class="grup">
                <label for="proposal">Proposal <span style="color:#b91c1c">*</span></label>
                <input class="input" type="file" id="proposal" name="proposal" accept=".pdf,.doc,.docx" required>
                @error('proposal') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="surat_penerimaan">Surat Penerimaan (jika ada)</label>
                <input class="input" type="file" id="surat_penerimaan" name="surat_penerimaan" accept=".pdf,.doc,.docx">
                @error('surat_penerimaan') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="berkas_lain">Berkas Lainnya (opsional)</label>
                <input class="input" type="file" id="berkas_lain" name="berkas_lain" accept=".pdf,.doc,.docx">
                @error('berkas_lain') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
        </div>

        <button class="btn" type="submit">Kirim Pengajuan</button>
        <a class="btn btn-abu" href="{{ route('mahasiswa.pengajuan.index') }}">Batal</a>
    </form>

    <script>
        function ubahMode() {
            var baru = document.querySelector('input[name="tempat_mode"]:checked').value === 'baru';
            document.getElementById('blok-baru').style.display = baru ? 'block' : 'none';
            document.getElementById('blok-pilih').style.display = baru ? 'none' : 'block';
        }
        ubahMode();
    </script>
    @endif
@endsection
