<a href="{{ route('dosen.verifikasi.index') }}" class="{{ request()->routeIs('dosen.verifikasi.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'verifikasi']) <span>Verifikasi Pengajuan</span></a>
<a href="{{ route('dosen.bimbingan.index') }}" class="{{ request()->routeIs('dosen.bimbingan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'bimbingan']) <span>Bimbingan</span></a>
<a href="{{ route('dosen.laporan.index') }}" class="{{ request()->routeIs('dosen.laporan.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'laporan']) <span>Laporan</span></a>
<a href="{{ route('dosen.penilaian.index') }}" class="{{ request()->routeIs('dosen.penilaian.*') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'nilai']) <span>Penilaian</span></a>
