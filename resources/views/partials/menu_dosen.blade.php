<a href="{{ route('dosen.verifikasi.index') }}" class="{{ request()->routeIs('dosen.verifikasi.*') ? 'aktif' : '' }}">Verifikasi Pengajuan</a>
<a href="{{ route('dosen.bimbingan.index') }}" class="{{ request()->routeIs('dosen.bimbingan.*') ? 'aktif' : '' }}">Bimbingan</a>
<a href="{{ route('dosen.laporan.index') }}" class="{{ request()->routeIs('dosen.laporan.*') ? 'aktif' : '' }}">Laporan</a>
<a href="{{ route('dosen.penilaian.index') }}" class="{{ request()->routeIs('dosen.penilaian.*') ? 'aktif' : '' }}">Penilaian</a>
