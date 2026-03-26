<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid License</title>
    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #1e293b;
            --card: rgba(255, 255, 255, 0.08);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --danger: #f87171;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 20% 20%, #1d4ed8 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, #dc2626 0%, transparent 35%),
                        linear-gradient(135deg, var(--bg-1), var(--bg-2));
            padding: 1.25rem;
        }

        .card {
            width: min(680px, 100%);
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            backdrop-filter: blur(8px);
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(2, 6, 23, 0.6);
        }

        h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2rem);
            color: var(--danger);
        }

        p {
            margin: 1rem 0 0;
            line-height: 1.6;
            color: var(--muted);
        }

        .reason {
            margin-top: 1rem;
            color: var(--text);
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Lisensi Tidak Valid</h1>
        <p>
            Aplikasi ini belum diaktivasi atau lisensi tidak dapat diverifikasi.
            Hubungi penjual untuk aktivasi lisensi atau isi data lisensi Mayar di panel admin.
        </p>
        <p class="reason">
            {{ $reason ?? 'Tidak ada detail kesalahan.' }}
        </p>
    </main>
</body>
</html>
