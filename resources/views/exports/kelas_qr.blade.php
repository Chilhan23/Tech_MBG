<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Kelas - {{ $kelas->nama_kelas }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .wrapper {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

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
            margin: 0 auto 20px auto;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
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

        .school-name { line-height: 1.1; text-align: left; }
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
        }

        .card-body {
            flex-grow: 1;
            padding: 15px;
            display: grid;
            grid-template-columns: 1fr 90px;
            align-items: center;
        }

        .main-info {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-row { display: flex; flex-direction: column; }

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

        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            padding: 5px;
        }

        .qr-label {
            font-size: 6px;
            color: #0068b3;
            font-weight: 800;
            margin-top: 4px;
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
        }

        button {
            background: #0068b3;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        @media print {
            @page { size: A4 portrait; margin: 0; }
            body { background: white; }
            .wrapper { box-shadow: none; padding: 20mm; }
            button, .header-text { display: none; }
            .card {
                margin: 0 auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header-text">
            <h1 style="margin-top:0; color:#0068b3; font-weight:800;">QR Kelas SMKN 5 Telkom</h1>
            <p style="color:#64748b; font-size:14px; margin-bottom:25px;">{{ $kelas->nama_kelas }}</p>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="logo-section">
                    <div class="instansi-logo">
                        <img src="https://smkn5telkom.sch.id/images/front/site/logo/Logo_SMKN5_Banda_Aceh.png" alt="Logo SMKN 5 Telkom BNA">
                    </div>
                    <div class="school-name">
                        <b>SMKN 5 TELKOM BNA</b>
                        <span>Banda Aceh</span>
                    </div>
                </div>
                <div class="card-type">QR KELAS MBG</div>
            </div>

            <div class="card-body">
                <div class="main-info">
                    <div class="info-row">
                        <label>Nama Kelas</label>
                        <span>{{ $kelas->nama_kelas }}</span>
                    </div>
                    <div class="info-row">
                        <label>Jumlah Siswa</label>
                        <span>{{ $kelas->students()->count() }} Siswa</span>
                    </div>
                    <div class="info-row">
                        <label>Tahun Ajaran</label>
                        <span>{{ now()->format('Y') }}</span>
                    </div>
                </div>

                <div class="qr-section">
                    {!! QrCode::size(75)->margin(0)->color(0, 104, 179)->generate($kelas->nama_kelas) !!}
                    <div class="qr-label">SCAN QR CODE</div>
                </div>
            </div>

            <div class="card-footer">
                <span>BERLAKU SELAMA TAHUN AJARAN AKTIF</span>
                <span style="color:#ffd700; font-weight:800">SMK BISA!</span>
            </div>
        </div>

        <button onclick="window.print()">Cetak QR Kelas</button>
    </div>
</body>
</html>