<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIM Magang') - SIM MAGANG</title>
    <style>
        :root { --biru:#12306b; --biru2:#1d4ed8; --abu:#f3f5f9; --teks:#1f2937; --garis:#e5e7eb; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: "Segoe UI", Arial, sans-serif; background:var(--abu); color:var(--teks); }
        .layout { display:flex; min-height:100vh; }
        .sidebar { width:240px; background:var(--biru); color:#fff; padding:20px 16px; display:flex; flex-direction:column; }
        .sidebar h2 { font-size:18px; margin:0 0 4px; }
        .sidebar small { opacity:.75; }
        .sidebar nav { margin-top:24px; flex:1; }
        .sidebar a, .sidebar span.nonaktif { display:block; padding:10px 12px; border-radius:8px; color:#fff; text-decoration:none; font-size:14px; margin-bottom:4px; }
        .sidebar a.aktif, .sidebar a:hover { background:rgba(255,255,255,.18); }
        .sidebar span.nonaktif { opacity:.45; cursor:not-allowed; }
        .sidebar form button { width:100%; padding:10px; border:0; border-radius:8px; background:#dc2626; color:#fff; cursor:pointer; font-size:14px; }
        .konten { flex:1; padding:28px; }
        .atas { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:8px; }
        .atas h1 { margin:0; font-size:22px; }
        .peran { background:#e0e7ff; color:var(--biru); padding:4px 12px; border-radius:999px; font-size:13px; }
        .kartu-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin-bottom:24px; }
        .kartu { background:#fff; border:1px solid var(--garis); border-radius:12px; padding:18px; }
        .kartu .label { font-size:13px; color:#6b7280; }
        .kartu .angka { font-size:28px; font-weight:700; margin-top:6px; color:var(--biru); }
        .kartu .teks { font-size:17px; font-weight:600; margin-top:8px; }
        .panel { background:#fff; border:1px solid var(--garis); border-radius:12px; padding:18px; margin-bottom:20px; }
        .panel h3 { margin:0 0 12px; font-size:16px; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--garis); }
        th { color:#6b7280; font-weight:600; }
        .kosong { color:#6b7280; font-size:14px; padding:6px 0; }
        .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .b-kuning { background:#fef3c7; color:#92400e; }
        .b-hijau  { background:#d1fae5; color:#065f46; }
        .b-merah  { background:#fee2e2; color:#991b1b; }
        .b-biru   { background:#dbeafe; color:#1e40af; }
        .b-abu    { background:#e5e7eb; color:#374151; }
        .info { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; padding:12px 14px; border-radius:10px; margin-bottom:18px; font-size:14px; }

        .btn { display:inline-block; padding:10px 18px; border:0; border-radius:8px; background:var(--biru2); color:#fff; font-size:14px; cursor:pointer; text-decoration:none; }
        .btn:hover { background:#1e40af; }
        .btn-abu { background:#6b7280; }
        .btn-kecil { padding:6px 12px; font-size:13px; }
        .grup { margin-bottom:16px; }
        .grup label { display:block; font-size:14px; font-weight:600; margin-bottom:6px; }
        .grup small { color:#6b7280; display:block; margin-top:4px; }
        .input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; background:#fff; }
        .input:focus { outline:2px solid #93c5fd; border-color:var(--biru2); }
        textarea.input { min-height:90px; resize:vertical; }
        .baris { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .galat-field { color:#b91c1c; font-size:13px; margin-top:4px; }
        .flash { padding:12px 14px; border-radius:10px; margin-bottom:18px; font-size:14px; }
        .flash-ok { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
        .flash-err { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .opsi { display:flex; gap:18px; margin-bottom:12px; }
        .opsi label { font-weight:400; display:flex; gap:6px; align-items:center; }
        .rincian th { width:190px; vertical-align:top; }
        @media (max-width:760px){ .baris{grid-template-columns:1fr} }
        @media (max-width:760px){ .layout{flex-direction:column} .sidebar{width:100%} }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2>SIM MAGANG</h2>
        <small>Fakultas Sains dan Teknologi</small>
        <nav>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*', 'mahasiswa.dashboard') ? 'aktif' : '' }}">Dashboard</a>
            @yield('menu')
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </aside>

    <main class="konten">
        <div class="atas">
            <h1>@yield('judul')</h1>
            <div>
                {{ auth()->user()->nama_lengkap }}
                <span class="peran">{{ auth()->user()->role }}</span>
            </div>
        </div>
        @if (session('sukses'))
            <div class="flash flash-ok">{{ session('sukses') }}</div>
        @endif
        @if (session('galat'))
            <div class="flash flash-err">{{ session('galat') }}</div>
        @endif
        @yield('isi')
    </main>
</div>
</body>
</html>
