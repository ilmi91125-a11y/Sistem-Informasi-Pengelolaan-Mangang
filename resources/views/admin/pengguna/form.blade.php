@extends('layouts.app')
@section('title', 'Akun Pengguna')
@section('judul', $u ? 'Ubah Akun' : 'Tambah Akun')
@section('menu') @include('partials.menu_admin') @endsection

@section('isi')
    @php
        $role = old('role', $u?->role ?? 'Mahasiswa');
        $dosen = $u?->dosen;
        $mhs = $u?->mahasiswa;
    @endphp

    <form method="POST" action="{{ $u ? route('admin.pengguna.update', $u->id_pengguna) : route('admin.pengguna.store') }}">
        @csrf
        @if ($u) @method('PUT') @endif

        <div class="panel">
            <h3>Data Akun</h3>

            @if ($u)
                <div class="grup"><label>Role</label><div>{{ $u->role }} <small style="color:#6b7280">(role tidak dapat diubah)</small></div></div>
            @else
                <div class="grup">
                    <label for="role">Role</label>
                    <select class="input" id="role" name="role" onchange="ubahRole()">
                        @foreach (['Mahasiswa', 'Dosen', 'Admin'] as $r)
                            <option value="{{ $r }}" @selected($role === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            @endif

            <div class="grup">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input class="input" id="nama_lengkap" name="nama_lengkap" maxlength="100" value="{{ old('nama_lengkap', $u?->nama_lengkap) }}" required>
                @error('nama_lengkap') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="baris">
                <div class="grup">
                    <label for="email">Email</label>
                    <input class="input" type="email" id="email" name="email" maxlength="100" value="{{ old('email', $u?->email) }}" required>
                    @error('email') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="no_telepon">No. Telepon</label>
                    <input class="input" id="no_telepon" name="no_telepon" maxlength="20" value="{{ old('no_telepon', $u?->no_telepon) }}">
                    @error('no_telepon') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="password">{{ $u ? 'Password Baru (kosongkan jika tidak diganti)' : 'Password' }}</label>
                <input class="input" type="text" id="password" name="password" autocomplete="off" minlength="8"
                       placeholder="Minimal 8 karakter" {{ $u ? '' : 'required' }}>
                @error('password') <div class="galat-field">{{ $message }}</div> @enderror
                @unless ($u) <small>Catat dan berikan password ini kepada pengguna.</small> @endunless
            </div>
            @if (! $u || $u->id_pengguna !== auth()->user()->id_pengguna)
                <label style="font-weight:400">
                    <input type="checkbox" name="status_aktif" value="1" @checked(old('status_aktif', $u ? $u->status_aktif : true))>
                    Akun aktif (dapat login)
                </label>
            @endif
        </div>

        <div class="panel" id="blok-Dosen">
            <h3>Profil Dosen</h3>
            <div class="baris">
                <div class="grup">
                    <label for="nidn">NIDN</label>
                    <input class="input" id="nidn" name="nidn" maxlength="20" value="{{ old('nidn', $dosen?->nidn) }}">
                    @error('nidn') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="bidang_keahlian">Bidang Keahlian</label>
                    <input class="input" id="bidang_keahlian" name="bidang_keahlian" maxlength="100" value="{{ old('bidang_keahlian', $dosen?->bidang_keahlian) }}">
                </div>
            </div>
        </div>

        <div class="panel" id="blok-Mahasiswa">
            <h3>Profil Mahasiswa</h3>
            <div class="baris">
                <div class="grup">
                    <label for="nim">NIM</label>
                    <input class="input" id="nim" name="nim" maxlength="15" value="{{ old('nim', $mhs?->nim) }}">
                    @error('nim') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
                <div class="grup">
                    <label for="angkatan">Angkatan</label>
                    <input class="input" type="number" id="angkatan" name="angkatan" value="{{ old('angkatan', $mhs?->angkatan) }}" placeholder="Contoh: 2024">
                    @error('angkatan') <div class="galat-field">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="grup">
                <label for="program_studi">Program Studi</label>
                <input class="input" id="program_studi" name="program_studi" maxlength="50" value="{{ old('program_studi', $mhs?->program_studi) }}">
                @error('program_studi') <div class="galat-field">{{ $message }}</div> @enderror
            </div>
            <div class="grup">
                <label for="id_dosen_pa">Dosen Penasihat Akademik</label>
                <select class="input" id="id_dosen_pa" name="id_dosen_pa">
                    <option value="">-- Pilih dosen PA --</option>
                    @foreach ($dosenList as $d)
                        <option value="{{ $d->id_dosen }}" @selected(old('id_dosen_pa', $mhs?->id_dosen_pa) === $d->id_dosen)>{{ $d->pengguna?->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('id_dosen_pa') <div class="galat-field">{{ $message }}</div> @enderror
                @if ($dosenList->isEmpty()) <small style="color:#b91c1c">Belum ada dosen. Buat akun Dosen terlebih dahulu.</small> @endif
            </div>
        </div>

        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-abu" href="{{ route('admin.pengguna.index') }}">Batal</a>
    </form>

    <script>
        var ROLE_TETAP = @json($u?->role);
        function ubahRole() {
            var el = document.getElementById('role');
            var r = ROLE_TETAP || (el ? el.value : 'Mahasiswa');
            document.getElementById('blok-Dosen').style.display = r === 'Dosen' ? 'block' : 'none';
            document.getElementById('blok-Mahasiswa').style.display = r === 'Mahasiswa' ? 'block' : 'none';
        }
        ubahRole();
    </script>
@endsection
