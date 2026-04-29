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
            width: 100%;
            max-width: 900px;
            padding: 30px;
            box-sizing: border-box;
            text-align: center;
        }

        .preview-card {
            background: white;
            border-radius: 24px;
            padding: 35px 30px;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            margin: 0 auto 20px auto;
            max-width: 700px;
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .logo-badge {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: white;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header-copy {
            text-align: left;
        }

        .header-copy h1 {
            margin: 0;
            font-size: 22px;
            color: #0f172a;
            letter-spacing: -0.02em;
            font-weight: 800;
        }

        .header-copy p {
            margin: 6px 0 0;
            color: #475569;
            font-size: 13px;
        }

        .qr-wrap {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 320px;
            padding: 25px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .qr-wrap .qr-label {
            margin-top: 16px;
            font-size: 10px;
            font-weight: 800;
            color: #0369a1;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .class-info {
            margin-top: 24px;
            text-align: center;
        }

        .class-info .info-card {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 320px;
            padding: 18px 24px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin: 0 auto;
        }

        .class-info .info-card label {
            display: block;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .class-info .info-card span {
            display: block;
            color: #0f172a;
            font-size: 22px;
            font-weight: 800;
        }

        .print-button {
            background: #0369a1;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 26px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(3, 105, 161, 0.18);
            transition: transform 0.2s ease;
        }

        .print-button:hover {
            transform: translateY(-1px);
        }

        @media print {
            @page { size: A4 portrait; margin: 0; }
            body { background: white; }
            .wrapper { padding: 0; }
            .print-button, .preview-header { display: none; }
            .preview-card {
                box-shadow: none;
                border: none;
                margin: 0;
                border-radius: 0;
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="preview-card">
            <div class="preview-header">
                <div class="logo-badge">
                    <img src="https://smkn5telkom.sch.id/images/front/site/logo/Logo_SMKN5_Banda_Aceh.png" alt="Logo SMKN 5 Telkom BNA">
                </div>
                <div class="header-copy">
                    <h1>QR Kelas SMKN 5 Telkom</h1>
                    <p>{{ $kelas->nama_kelas }}</p>
                </div>
            </div>

            <div class="qr-wrap">
                {!! QrCode::size(220)->margin(0)->color(0, 104, 179)->generate($kelas->nama_kelas) !!}
                <div class="qr-label">Scan QR Kelas</div>
            </div>

            <div class="class-info">
                <div class="info-card">
                    <label>Nama Kelas</label>
                    <span>{{ $kelas->nama_kelas }}</span>
                </div>
            </div>
        </div>

        <button class="print-button" onclick="window.print()">Cetak QR Kelas</button>
    </div>
</body>
</html>