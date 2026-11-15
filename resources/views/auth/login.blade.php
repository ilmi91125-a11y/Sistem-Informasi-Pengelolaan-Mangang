<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIM MAGANG</title>
    <style>
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Segoe UI", Arial, sans-serif; min-height:100vh; display:flex; }
        .kiri { flex:1; background:#12306b; color:#fff; padding:48px; display:flex; flex-direction:column; justify-content:center; }
        .kiri h1 { margin:0 0 6px; font-size:32px; }
        .kiri p.sub { margin:0 0 36px; opacity:.8; }
        .kiri ul { list-style:none; padding:0; margin:0; }
        .kiri li { margin-bottom:16px; padding-left:28px; position:relative; line-height:1.5; }
        .kiri li::before { content:"✓"; position:absolute; left:0; color:#93c5fd; font-weight:700; }
        .kanan { flex:1; display:flex; align-items:center; justify-content:center; padding:32px; background:#f8fafc; }
        .form { width:100%; max-width:380px; }
        .form h2 { margin:0 0 6px; }
        .form p.sub { margin:0 0 24px; color:#6b7280; font-size:14px; }
        label { display:block; font-size:14px; margin-bottom:6px; font-weight:600; }
        .input { width:100%; padding:11px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:15px; margin-bottom:16px; }
        .input:focus { outline:2px solid #93c5fd; border-color:#1d4ed8; }
        .pw { position:relative; }
        .pw button { position:absolute; right:8px; top:6px; border:0; background:none; cursor:pointer; color:#1d4ed8; font-size:13px; }
        .tombol { width:100%; padding:12px; border:0; border-radius:8px; background:#1d4ed8; color:#fff; font-size:16px; cursor:pointer; }
        .tombol:hover { background:#1e40af; }
        .galat { background:#fee2e2; color:#991b1b; padding:10px 12px; border-radius:8px; margin-bottom:16px; font-size:14px; }
        .bantuan { margin-top:20px; font-size:13px; color:#6b7280; text-align:center; }
        @media (max-width:800px){ body{flex-direction:column} .kiri{padding:28px} }
    </style>
</head>
<body>
    <section class="kiri">
        <h1>SIM MAGANG</h1>
        <p class="sub">Fakultas Sains dan Teknologi</p>
        <ul>
            <li>Pengelolaan proses magang secara digital dan terintegrasi.</li>
            <li>Verifikasi, pengelolaan data, penilaian, dan rekapitulasi magang.</li>
        </ul>
    </section>

    <section class="kanan">
        <div class="form">
            <h2>Masuk ke Akun</h2>
            <p class="sub">Gunakan email dan password yang terdaftar.</p>

            @if ($errors->any())
                <div class="galat">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.proses') }}">
                @csrf
                <label for="email">Email</label>
                <input class="input" type="email" id="email" name="email"
                       value="{{ old('email') }}" placeholder="nama@contoh.com" required autofocus>

                <label for="password">Password</label>
                <div class="pw">
                    <input class="input" type="password" id="password" name="password"
                           placeholder="Masukkan password" required>
                    <button type="button" onclick="
                        var p = document.getElementById('password');
                        p.type = p.type === 'password' ? 'text' : 'password';
                        this.textContent = p.type === 'password' ? 'Tampilkan' : 'Sembunyikan';
                    ">Tampilkan</button>
                </div>

                <button class="tombol" type="submit">Masuk</button>
            </form>

            <p class="bantuan">Akses khusus pengguna terdaftar. Hubungi administrator bila butuh bantuan.</p>
        </div>
    </section>
</body>
</html>
