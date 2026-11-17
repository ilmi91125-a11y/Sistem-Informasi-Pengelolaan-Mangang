<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIM MAGANG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sim.css') }}">
    <style>
        body { min-height:100vh; display:grid; place-items:center; padding:32px 20px; }
        /* Kartu putih dengan lapisan navy di belakangnya, seperti rancangan Figma */
        .kartu-login { position:relative; width:100%; max-width:1000px; }
        .kartu-login::before { content:""; position:absolute; inset:18px -18px -18px 18px; background:var(--navy); border-radius:22px; }
        .kartu-isi { position:relative; display:grid; grid-template-columns:1fr 1.05fr; background:#fff; border-radius:22px; overflow:hidden; box-shadow:0 20px 50px rgba(11,36,84,.18); }
        .panel-kiri { background:var(--navy); color:#fff; padding:56px 44px; display:flex; flex-direction:column; justify-content:center; background-image:radial-gradient(circle at 100% 0, rgba(49,129,234,.35), transparent 55%); }
        .logo-besar { width:56px; height:56px; border-radius:16px; background:var(--blue); display:grid; place-items:center; font-weight:700; font-size:20px; margin-bottom:22px; }
        .panel-kiri h1 { margin:0; font-size:30px; line-height:1.2; letter-spacing:.03em; }
        .panel-kiri .sub { margin:6px 0 34px; opacity:.8; font-size:14px; }
        .panel-kiri ul { list-style:none; margin:0; padding:0; }
        .panel-kiri li { position:relative; padding-left:34px; margin-bottom:18px; font-size:13.5px; line-height:1.6; opacity:.95; }
        .panel-kiri li::before { content:"✓"; position:absolute; left:0; top:1px; width:22px; height:22px; border-radius:50%; background:rgba(255,255,255,.16); display:grid; place-items:center; font-size:12px; font-weight:700; }
        .panel-kanan { padding:56px 52px; display:flex; flex-direction:column; justify-content:center; }
        .panel-kanan h2 { margin:0 0 4px; font-size:26px; font-weight:700; color:var(--navy); letter-spacing:.02em; }
        .panel-kanan .sub { margin:0 0 28px; color:var(--muted); font-size:13.5px; }
        .pw { position:relative; }
        .pw .input { padding-right:48px; }
        .pw button { position:absolute; right:6px; top:50%; transform:translateY(-50%); border:0; background:none; cursor:pointer; color:var(--muted); width:38px; height:38px; border-radius:8px; display:grid; place-items:center; }
        .pw button:hover { color:var(--navy); background:#EEF1F9; }
        .pw svg { width:20px; height:20px; }
        .tombol { width:100%; padding:13px; margin-top:6px; font-size:16px; font-weight:700; border-radius:10px; }
        .galat { background:#FDE0E0; color:#8F1111; border:1px solid #F7B7B7; padding:11px 14px; border-radius:10px; margin-bottom:18px; font-size:13px; }
        .bantuan { margin:26px 0 0; text-align:center; font-size:12.5px; color:var(--muted); line-height:1.7; }
        .bantuan b { display:block; color:var(--navy); font-weight:600; }
        @media (max-width:820px) {
            .kartu-isi { grid-template-columns:1fr; }
            .panel-kiri { padding:32px 28px; }
            .panel-kiri .sub { margin-bottom:20px; }
            .panel-kanan { padding:36px 28px; }
            .kartu-login::before { inset:12px -8px -12px 8px; }
        }
    </style>
</head>
<body>
    <div class="kartu-login">
        <div class="kartu-isi">
            <section class="panel-kiri">
                <div class="logo-besar">SM</div>
                <h1>SIM MAGANG</h1>
                <p class="sub">Fakultas Sains dan Teknologi</p>
                <ul>
                    <li>Pengelolaan proses magang secara digital dan terintegrasi.</li>
                    <li>Verifikasi, pengelolaan data, penilaian, dan rekapitulasi magang.</li>
                </ul>
            </section>

            <section class="panel-kanan">
                <h2>MASUK KE AKUN</h2>
                <p class="sub">Masukkan akun untuk melanjutkan ke sistem.</p>

                @if ($errors->any())
                    <div class="galat" role="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.proses') }}">
                    @csrf
                    <div class="grup">
                        <label for="email">Username/Email</label>
                        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="Masukkan username" autocomplete="username" required autofocus>
                    </div>

                    <div class="grup">
                        <label for="password">Password</label>
                        <div class="pw">
                            <input class="input" type="password" id="password" name="password"
                                   placeholder="Masukkan password" autocomplete="current-password" required>
                            <button type="button" id="lihat" aria-label="Tampilkan password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path id="mata" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                                    <path id="coret" d="M4 4l16 16" style="display:none"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button class="btn tombol" type="submit">Masuk</button>
                </form>

                <p class="bantuan"><b>Akses khusus pengguna terdaftar</b>Butuh bantuan? Hubungi administrator.</p>
            </section>
        </div>
    </div>
    <script>
        document.getElementById('lihat').addEventListener('click', function () {
            var p = document.getElementById('password');
            var tampil = p.type === 'password';
            p.type = tampil ? 'text' : 'password';
            document.getElementById('coret').style.display = tampil ? '' : 'none';
            this.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>
