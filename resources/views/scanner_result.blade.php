<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Scan QR</title>
    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            width: min(540px, 100%);
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            text-align: center;
        }
        .title {
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .message {
            margin: 18px 0;
            color: #334155;
            line-height: 1.75;
        }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            padding: 14px 18px;
            border-radius: 14px;
            font-weight: 600;
        }
        .status.success { background: #ecfdf5; color: #166534; }
        .status.error { background: #fef2f2; color: #991b1b; }
        a.button {
            display: inline-block;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">Hasil Scan QR</div>
        <div class="status {{ $success ? 'success' : 'error' }}">
            {{ $success ? 'Berhasil' : 'Gagal' }}
        </div>
        <div class="message">{{ $message }}</div>
        <a class="button" href="{{ route('scanner.index') }}">Kembali ke Scanner</a>
    </div>
</body>
</html>
