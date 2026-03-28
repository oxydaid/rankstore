<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer Wizard</title>
    <style>
        :root {
            --bg-1: #fff7ed;
            --bg-2: #ffedd5;
            --card: #ffffff;
            --line: #fed7aa;
            --text: #7c2d12;
            --muted: #9a3412;
            --accent: #ea580c;
            --accent-2: #c2410c;
            --ok: #16a34a;
            --bad: #dc2626;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top right, var(--bg-2), var(--bg-1));
            min-height: 100vh;
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .container {
            width: 100%;
            max-width: 920px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 14px 45px rgba(124, 45, 18, 0.12);
            overflow: hidden;
        }

        .header {
            padding: 24px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(90deg, rgba(234, 88, 12, 0.09), rgba(194, 65, 12, 0.05));
        }

        .title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .subtitle {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .steps {
            padding: 16px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .step-pill {
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 6px 12px;
            font-size: 0.82rem;
            background: #fff;
            color: var(--muted);
        }

        .step-pill.active {
            border-color: var(--accent);
            color: #fff;
            background: var(--accent);
        }

        .content {
            padding: 24px;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.9rem;
            margin-bottom: 16px;
            border: 1px solid;
        }

        .alert-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .alert-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .alert-warning {
            border-color: #fde68a;
            background: #fffbeb;
            color: #92400e;
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        label {
            display: block;
            font-size: 0.88rem;
            color: var(--muted);
            margin-bottom: 6px;
        }

        input, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
            color: var(--text);
            background: #fff;
        }

        textarea { min-height: 88px; resize: vertical; }

        .requirement-list {
            display: grid;
            gap: 10px;
            margin-bottom: 20px;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: #fffbeb;
        }

        .status-ok { color: var(--ok); font-weight: 700; }
        .status-bad { color: var(--bad); font-weight: 700; }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 0.94rem;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            background: var(--accent);
        }

        button:hover { background: var(--accent-2); }

        .btn-secondary {
            background: #cbd5e1;
            color: #1e293b;
        }

        .btn-secondary:hover { background: #94a3b8; }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .container { border-radius: 14px; }
        }
    </style>
</head>
<body>
    @php
        $steps = [
            'requirements' => '1. Requirements',
            'database' => '2. Database',
            'app-settings' => '3. Lisensi & Setting',
            'admin-user' => '4. Admin',
            'finish' => '5. Selesai',
        ];
    @endphp

    <main class="container">
        <header class="header">
            <h1 class="title">Installer Wizard</h1>
            <p class="subtitle">Panduan setup awal aplikasi dari server requirement sampai akun admin.</p>
        </header>

        <div class="steps">
            @foreach ($steps as $stepKey => $label)
                <span class="step-pill {{ $step === $stepKey ? 'active' : '' }}">{{ $label }}</span>
            @endforeach
        </div>

        <section class="content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if ($step === 'requirements')
                <div class="requirement-list">
                    @foreach ($checks as $check)
                        <div class="requirement-item">
                            <span>{{ $check['label'] }}</span>
                            <span class="{{ $check['ok'] ? 'status-ok' : 'status-bad' }}">
                                {{ $check['ok'] ? 'OK' : 'Tidak tersedia' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('installer.requirements.store') }}">
                    @csrf
                    <div class="actions">
                        <button type="submit">Lanjut Setup Database</button>
                    </div>
                </form>
            @endif

            @if ($step === 'database')
                <form method="POST" action="{{ route('installer.database.store') }}" class="grid">
                    @csrf
                    <div class="grid grid-2">
                        <div>
                            <label for="db_host">DB Host</label>
                            <input id="db_host" name="db_host" value="{{ old('db_host', $defaults['db_host']) }}" required>
                        </div>
                        <div>
                            <label for="db_port">DB Port</label>
                            <input id="db_port" name="db_port" type="number" value="{{ old('db_port', $defaults['db_port']) }}" required>
                        </div>
                        <div>
                            <label for="db_database">DB Name</label>
                            <input id="db_database" name="db_database" value="{{ old('db_database', $defaults['db_database']) }}" required>
                        </div>
                        <div>
                            <label for="db_username">DB Username</label>
                            <input id="db_username" name="db_username" value="{{ old('db_username', $defaults['db_username']) }}" required>
                        </div>
                    </div>

                    <div>
                        <label for="db_password">DB Password</label>
                        <input id="db_password" name="db_password" type="password" value="{{ old('db_password', $defaults['db_password']) }}">
                    </div>

                    <div class="actions">
                        <button type="submit">Simpan & Jalankan Migration</button>
                    </div>
                </form>
            @endif

            @if ($step === 'app-settings')
                <form method="POST" action="{{ route('installer.app-settings.store') }}" class="grid">
                    @csrf
                    <div class="grid grid-2">
                        <div>
                            <label for="app_url">App URL</label>
                            <input id="app_url" name="app_url" type="url" value="{{ old('app_url', $defaults['app_url']) }}" required placeholder="https://domain.com">
                        </div>
                        <div>
                            <label for="site_name">Nama Website</label>
                            <input id="site_name" name="site_name" value="{{ old('site_name', $defaults['site_name']) }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="site_description">Deskripsi</label>
                        <textarea id="site_description" name="site_description">{{ old('site_description', $defaults['site_description']) }}</textarea>
                    </div>

                    <div class="grid grid-2">
                        <div>
                            <label for="license_code">License Code</label>
                            <input id="license_code" name="license_code" value="{{ old('license_code', $defaults['license_code']) }}" required>
                        </div>
                        <div>
                            <label for="mayar_product_id">Mayar Product ID</label>
                            <input id="mayar_product_id" name="mayar_product_id" value="{{ old('mayar_product_id', $defaults['mayar_product_id']) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-2">
                        <div>
                            <label for="server_ip">IP Server</label>
                            <input id="server_ip" name="server_ip" value="{{ old('server_ip', $defaults['server_ip']) }}">
                        </div>
                        <div>
                            <label for="server_port">Port Server</label>
                            <input id="server_port" name="server_port" value="{{ old('server_port', $defaults['server_port']) }}">
                        </div>
                    </div>

                    <div class="grid grid-2">
                        <div>
                            <label for="primary_color">Primary Color</label>
                            <input id="primary_color" name="primary_color" value="{{ old('primary_color', $defaults['primary_color']) }}" required>
                        </div>
                        <div>
                            <label for="secondary_color">Secondary Color</label>
                            <input id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $defaults['secondary_color']) }}" required>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit">Simpan & Verifikasi Lisensi</button>
                    </div>
                </form>
            @endif

            @if ($step === 'admin-user')
                <form method="POST" action="{{ route('installer.admin-user.store') }}" class="grid">
                    @csrf
                    <div>
                        <label for="name">Nama Admin</label>
                        <input id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label for="email">Email Admin</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="grid grid-2">
                        <div>
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" required>
                        </div>
                        <div>
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit">Simpan Admin</button>
                    </div>
                </form>
            @endif

            @if ($step === 'finish')
                <div class="alert alert-success">
                    Semua step selesai. Klik tombol di bawah untuk finalisasi instalasi.
                </div>

                <form method="POST" action="{{ route('installer.complete') }}">
                    @csrf
                    <div class="actions">
                        <button type="submit">Selesaikan Instalasi</button>
                    </div>
                </form>
            @endif

            @if ($step === 'success')
                <div class="alert alert-success">
                    <strong>Instalasi Berhasil!</strong><br>
                    Website Anda sedang dimuat. Mohon tunggu beberapa detik (mengalihkan otomatis)...
                    <script>
                        setTimeout(function() {
                            window.location.href = "{{ route('home') }}";
                        }, 2500);
                    </script>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
