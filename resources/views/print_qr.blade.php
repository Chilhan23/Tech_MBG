<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Siswa</title>
    <style>
        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .container {
            max-width: 720px;
            margin: 32px auto;
            padding: 24px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .card {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: center;
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
        }
        .info {
            display: grid;
            gap: 12px;
        }
        .info b {
            display: block;
            color: #111827;
        }
        .qr {
            background: white;
            border-radius: 18px;
            padding: 18px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        button {
            margin-top: 8px;
            padding: 12px 18px;
            border: none;
            border-radius: 14px;
            background: #2563eb;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>QR Code Siswa</h1>
                <p>Gunakan tombol cetak untuk membuat kartu QR Code.</p>
            </div>
            <button type="button" onclick="window.print()">Cetak</button>
        </div>

        <div class="card">
            <div class="info">
                <div><b>Nama:</b> {{ $student->name }}</div>
                <div><b>NISN:</b> {{ $student->nisn }}</div>
                <div><b>Kelas:</b> {{ $student->kelas }}</div>
                <div><b>Jurusan:</b> {{ $student->jurusan }}</div>
            </div>
            <div class="qr">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)->generate(route('scanner.public_scan', ['nisn' => $student->nisn], true)) !!}
            </div>
        </div>
    </div>
</body>
</html>
