<a href="{{ route('dosen.verifikasi.index') }}" class="{{ request()->routeIs('dosen.verifikasi.*') ? 'aktif' : '' }}">Verifikasi Pengajuan</a>
<a href="{{ route('dosen.bimbingan.index') }}" class="{{ request()->routeIs('dosen.bimbingan.*') ? 'aktif' : '' }}">Bimbingan</a>
<span class="nonaktif">Laporan</span>
<span class="nonaktif">Penilaian</span>
