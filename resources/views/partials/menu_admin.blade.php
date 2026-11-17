<a href="{{ route('admin.ketentuan.index') }}" class="{{ request()->routeIs('admin.ketentuan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'info']) <span>Ketentuan Magang</span></a>
<a href="{{ route('admin.tempat.index') }}" class="{{ request()->routeIs('admin.tempat.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'tempat']) <span>Tempat Magang</span></a>
<a href="{{ route('admin.surat.index') }}" class="{{ request()->routeIs('admin.surat.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'surat']) <span>Surat Magang</span></a>
<a href="{{ route('admin.penempatan.index') }}" class="{{ request()->routeIs('admin.penempatan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'jadwal']) <span>Penempatan &amp; Jadwal</span></a>
<a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'user']) <span>Pengguna &amp; Role</span></a>
<a href="{{ route('admin.mahasiswa.index') }}" class="{{ request()->routeIs('admin.mahasiswa.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'users']) <span>Data Mahasiswa</span></a>
<a href="{{ route('admin.rekap.index') }}" class="{{ request()->routeIs('admin.rekap.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'rekap']) <span>Rekapitulasi</span></a>
