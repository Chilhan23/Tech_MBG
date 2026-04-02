<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Siswa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        /* Tombol print — hilang saat print */
        .print-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto 20px;
            padding: 14px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .print-bar span {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 600;
        }
        .print-bar button {
            padding: 10px 24px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }
        .print-bar button:hover { background: #1d4ed8; }

        /* Grid kartu QR */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            max-width: 900px;
            margin: 0 auto;
        }

        .qr-card {
            background: white;
            border-radius: 12px;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            page-break-inside: avoid;
        }

        .qr-card svg, .qr-card img {
            width: 160px;
            height: 160px;
            display: block;
        }

        .qr-card .name {
            margin-top: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
        }

        .qr-card .nisn {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 3px;
            font-family: monospace;
        }

        .qr-card .kelas {
            font-size: 0.72rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Print styles */
        @media print {
            body { background: white; padding: 0; }
            .print-bar { display: none; }
            .grid {
                max-width: 100%;
                gap: 8px;
                grid-template-columns: repeat(3, 1fr);
            }
            .qr-card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 10px 8px;
            }
            .qr-card svg, .qr-card img {
                width: 130px;
                height: 130px;
            }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <span>{{ $students->count() }} siswa dipilih</span>
    <button onclick="window.print()">🖨️ Cetak Semua</button>
</div>

<div class="grid">
    @foreach ($students as $student)
        <div class="qr-card">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate($student->nisn) !!}
            <div class="name">{{ $student->name }}</div>
            <div class="nisn">{{ $student->nisn }}</div>
            <div class="kelas">{{ $student->kelas }} &middot; {{ $student->jurusan }}</div>
        </div>
    @endforeach
</div>

</body>
</html>