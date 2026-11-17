<a href="{{ route('admin.ketentuan.index') }}" class="{{ request()->routeIs('admin.ketentuan.*') ? 'aktif' : '' }}">Ketentuan Magang</a>
<a href="{{ route('admin.tempat.index') }}" class="{{ request()->routeIs('admin.tempat.*') ? 'aktif' : '' }}">Tempat Magang</a>
<a href="{{ route('admin.surat.index') }}" class="{{ request()->routeIs('admin.surat.*') ? 'aktif' : '' }}">Surat Magang</a>
<a href="{{ route('admin.penempatan.index') }}" class="{{ request()->routeIs('admin.penempatan.*') ? 'aktif' : '' }}">Penempatan &amp; Jadwal</a>
<a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'aktif' : '' }}">Pengguna &amp; Role</a>
<a href="{{ route('admin.mahasiswa.index') }}" class="{{ request()->routeIs('admin.mahasiswa.*') ? 'aktif' : '' }}">Data Mahasiswa</a>
<a href="{{ route('admin.rekap.index') }}" class="{{ request()->routeIs('admin.rekap.*') ? 'aktif' : '' }}">Rekapitulasi</a>
