<a href="{{ route('mahasiswa.informasi') }}" class="{{ request()->routeIs('mahasiswa.informasi') ? 'aktif' : '' }}">Informasi Magang</a>
<a href="{{ route('mahasiswa.pendaftaran') }}" class="{{ request()->routeIs('mahasiswa.pendaftaran*') ? 'aktif' : '' }}">Pendaftaran Magang</a>
<a href="{{ route('mahasiswa.pengajuan.index') }}" class="{{ request()->routeIs('mahasiswa.pengajuan.*') ? 'aktif' : '' }}">Pengajuan Lokasi</a>
<a href="{{ route('mahasiswa.surat.index') }}" class="{{ request()->routeIs('mahasiswa.surat.*') ? 'aktif' : '' }}">Surat Magang</a>
<a href="{{ route('mahasiswa.bimbingan.index') }}" class="{{ request()->routeIs('mahasiswa.bimbingan.*') ? 'aktif' : '' }}">Bimbingan</a>
<a href="{{ route('mahasiswa.laporan.index') }}" class="{{ request()->routeIs('mahasiswa.laporan.*') ? 'aktif' : '' }}">Laporan</a>
<span class="nonaktif">Penilaian</span>
