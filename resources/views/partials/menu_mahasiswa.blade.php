<a href="{{ route('mahasiswa.informasi') }}" class="{{ request()->routeIs('mahasiswa.informasi') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'info']) <span>Informasi Magang</span></a>
<a href="{{ route('mahasiswa.pendaftaran') }}" class="{{ request()->routeIs('mahasiswa.pendaftaran*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'daftar']) <span>Pendaftaran Magang</span></a>
<a href="{{ route('mahasiswa.pengajuan.index') }}" class="{{ request()->routeIs('mahasiswa.pengajuan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'lokasi']) <span>Pengajuan Lokasi</span></a>
<a href="{{ route('mahasiswa.surat.index') }}" class="{{ request()->routeIs('mahasiswa.surat.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'surat']) <span>Surat Magang</span></a>
<a href="{{ route('mahasiswa.bimbingan.index') }}" class="{{ request()->routeIs('mahasiswa.bimbingan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'bimbingan']) <span>Bimbingan</span></a>
<a href="{{ route('mahasiswa.laporan.index') }}" class="{{ request()->routeIs('mahasiswa.laporan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'laporan']) <span>Laporan</span></a>
<a href="{{ route('mahasiswa.penilaian.index') }}" class="{{ request()->routeIs('mahasiswa.penilaian.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'nilai']) <span>Penilaian</span></a>
