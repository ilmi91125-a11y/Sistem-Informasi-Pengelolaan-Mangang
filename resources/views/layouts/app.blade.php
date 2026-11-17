<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIM Magang') - SIM MAGANG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sim.css') }}">
</head>
<body>
@php
    $nama = auth()->user()->nama_lengkap;
    $inisial = collect(preg_split('/\s+/', trim($nama)))->filter()->take(2)->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
@endphp
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="logo">SM</div>
            <div>
                <b>SIM MAGANG</b>
                <small>Fakultas Sains dan Teknologi</small>
            </div>
        </div>
        <div class="menu-label">MENU</div>
        <nav aria-label="Menu utama">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*', 'mahasiswa.dashboard') ? 'aktif' : '' }}">@include('partials.icon', ['n' => 'home']) <span>Dashboard</span></a>
            @yield('menu')
        </nav>
        <form class="logout" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">@include('partials.icon', ['n' => 'logout']) Logout</button>
        </form>
    </aside>

    <main class="konten">
        <div class="atas">
            <div class="kiri">
                <button class="menu-toggle" type="button" aria-label="Buka menu" aria-controls="sidebar"
                        onclick="document.body.classList.toggle('menu-buka')">@include('partials.icon', ['n' => 'menu'])</button>
                <h1>@yield('judul')</h1>
            </div>
            <div class="pengguna">
                <div class="avatar">{{ $inisial }}</div>
                <div>
                    <div class="nama">{{ $nama }}</div>
                    <span class="peran">{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
        @if (session('sukses'))
            <div class="flash flash-ok" role="status"><span>{{ session('sukses') }}</span><button type="button" aria-label="Tutup" onclick="this.parentNode.remove()">&times;</button></div>
        @endif
        @if (session('galat'))
            <div class="flash flash-err" role="alert"><span>{{ session('galat') }}</span><button type="button" aria-label="Tutup" onclick="this.parentNode.remove()">&times;</button></div>
        @endif
        @yield('isi')
    </main>
</div>
<script>
    // Tutup menu seluler saat klik di luar sidebar
    document.addEventListener('click', function (e) {
        if (document.body.classList.contains('menu-buka') && !e.target.closest('#sidebar, .menu-toggle')) {
            document.body.classList.remove('menu-buka');
        }
    });
</script>
</body>
</html>
