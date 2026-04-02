<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Siswa Massal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            padding: 20px;
        }

        /* Tombol print — hilang saat print */
        .print-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto 20px;
            padding: 14px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .print-bar button {
            padding: 10px 24px;
            background: #0068b3;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        /* Grid kartu QR (Tetap 3 Kolom sesuai request) */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* =========================================
           STRUKTUR KARTU (SAMA PERSIS 100%)
           ========================================= */
        .card {
            width: 85.6mm;
            height: 54mm;
            border-radius: 12px;
            background: #ffffff;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            padding: 0;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            page-break-inside: avoid; /* Biar kartu nggak kepotong antar halaman */
        }

        .card-header {
            background: #0068b3;
            height: 48px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 3;
            color: white;
        }

        .instansi-logo {
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid white;
        }
        
        .instansi-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .school-name {
            line-height: 1.1;
            text-align: left;
        }

        .school-name b { font-size: 11px; text-transform: uppercase; font-weight: 800; }
        .school-name span { font-size: 8px; display: block; opacity: 0.9; }

        .card-type {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            background: #ffd700;
            color: #0068b3;
            padding: 2px 7px;
            border-radius: 20px;
            margin-left: auto;
            z-index: 3;
        }

        .card-body {
            flex-grow: 1;
            padding: 15px;
            display: grid;
            grid-template-columns: 1fr 90px;
            z-index: 3;
            align-items: center;
        }

        .main-info {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
        }

        .info-row label {
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
        }

        .info-row span {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
        }

        .card-footer {
            background: #0068b3;
            height: 25px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            font-size: 8px;
            color: white;
            font-weight: 600;
            justify-content: space-between;
            position: relative;
        }

        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            padding: 5px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);
        }

        .qr-label {
            font-size: 6px;
            color: #0068b3;
            font-weight: 800;
            margin-top: 4px;
        }

        /* Print styles */
        @media print {
            body { background: white; padding: 0; }
            .print-bar { display: none; }
            .grid {
                max-width: 100%;
                gap: 10px;
                grid-template-columns: repeat(2, 1fr); /* Otomatis jadi 2 kolom saat print biar pas di A4 */
            }
            .card { 
                box-shadow: none;
                border: 1px solid #eee;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <span style="font-weight: 800; color: #0068b3;">LIST CETAK KARTU ({{ $students->count() }} SISWA)</span>
    <button onclick="window.print()">🖨️ Cetak Semua</button>
</div>

<div class="grid">
    @foreach ($students as $student)
        <div class="card">
            <div class="card-header">
                <div class="logo-section">
                    <div class="instansi-logo">
                        <img src="https://smkn5telkom.sch.id/images/front/site/logo/Logo_SMKN5_Banda_Aceh.png" alt="Logo">
                    </div>
                    <div class="school-name">
                        <b>SMKN 5 TELKOM BNA</b>
                        <span>Banda Aceh</span>
                    </div>
                </div>
                <div class="card-type">E-KARTU SISWA</div>
            </div>

            <div class="card-body">
                <div class="main-info">
                    <div class="info-row">
                        <label>Nama Lengkap</label>
                        <span>{{ $student->name }}</span>
                    </div>
                    <div class="info-row">
                        <label>NISN</label>
                        <span style="letter-spacing: 0.5px;">{{ $student->nisn }}</span>
                    </div>
                    <div class="info-row">
                        <label>Kelas / Jurusan</label>
                        <span>{{ $student->kelas }} / {{ $student->jurusan }}</span>
                    </div>
                </div>

                <div class="qr-section">
                    {!! QrCode::size(75)->margin(0)->color(0, 104, 179)->generate($student->nisn) !!}
                    <div class="qr-label">SCAN QR CODE</div>
                </div>
            </div>

            <div class="card-footer">
                <span>BERLAKU SELAMA MENJADI SISWA AKTIF</span>
                <span style="color:#ffd700; font-weight:800">SMK BISA!</span>
            </div>
        </div>
    @endforeach
</div>

</body>
</html>