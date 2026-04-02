<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner MBG</title>
    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            color: #111827;
        }
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 720px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            padding: 24px;
        }
        .status {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-weight: 600;
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
        .status.success { background: #ecfdf5; color: #166534; }
        .status.error { background: #fef2f2; color: #991b1b; }
        .scanner-area {
            width: 100%;
            min-height: 320px;
            border-radius: 18px;
            overflow: hidden;
            background: #111827;
        }
        .hint {
            margin-top: 18px;
            color: #6b7280;
            line-height: 1.75;
        }
        .manual-form {
            margin-top: 22px;
            display: grid;
            gap: 12px;
        }
        .manual-form input,
        .manual-form button {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .manual-form button {
            background: #2563eb;
            color: white;
            border-color: transparent;
            cursor: pointer;
        }
        .manual-form button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Scanner QR MBG</h1>
            <p class="hint">Arahkan kamera ke QR Code siswa atau masukkan NISN secara manual jika kamera tidak tersedia.</p>

            @if(session('success'))
                <div class="status success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="status error">{{ session('error') }}</div>
            @endif

            <div id="reader" class="scanner-area"></div>
            <p id="message" class="hint">Menunggu scan... izinkan akses kamera ketika diminta.</p>

            <form id="scan-form" method="POST" action="{{ route('scanner.store') }}">
                @csrf
                <input type="hidden" name="nisn" id="nisn" />
            </form>

            <div class="manual-form">
                <label for="manual-nisn">Masukkan NISN secara manual</label>
                <input id="manual-nisn" type="text" placeholder="NISN siswa" />
                <button type="button" id="manual-submit">Catat Absensi</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.7/html5-qrcode.min.js"></script>
    <script>
        const nisnInput = document.getElementById('nisn');
        const message = document.getElementById('message');
        const scanForm = document.getElementById('scan-form');
        const manualSubmit = document.getElementById('manual-submit');
        const manualNisn = document.getElementById('manual-nisn');

        let html5QrCode = null;
        let html5QrCodeScanner = null;

        manualSubmit.addEventListener('click', () => {
            if (!manualNisn.value.trim()) {
                message.textContent = 'Masukkan NISN terlebih dahulu.';
                return;
            }
            nisnInput.value = manualNisn.value.trim();
            scanForm.submit();
        });

        function submitScan() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    scanForm.submit();
                }).catch(() => {
                    scanForm.submit();
                });
                return;
            }

            if (html5QrCodeScanner) {
                html5QrCodeScanner.clear().then(() => {
                    scanForm.submit();
                }).catch(() => {
                    scanForm.submit();
                });
                return;
            }

            scanForm.submit();
        }

        function getNisnFromDecodedText(decodedText) {
            try {
                const url = new URL(decodedText);

                if (url.searchParams.has('nisn')) {
                    return url.searchParams.get('nisn');
                }
            } catch (error) {
                
            }

            return decodedText;
        }

        function onScanSuccess(decodedText) {
            if (!decodedText) {
                return;
            }

            const scannedValue = getNisnFromDecodedText(decodedText);
            nisnInput.value = scannedValue;
            message.textContent = 'QR Code terdeteksi: ' + scannedValue + '. Mengirimkan data...';
            submitScan();
        }

        function onScanFailure(error) {
            console.debug('Scan gagal:', error);
            message.textContent = 'Scan gagal. Coba pilih file QR atau gunakan kamera lain.';
        }

        function startScanner() {
            try {
                html5QrCodeScanner = new Html5QrcodeScanner(
                    'reader',
                    {
                        fps: 10,
                        qrbox: { width: 280, height: 280 },
                        rememberLastUsedCamera: true,
                        showTorchButtonIfSupported: true,
                        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA, Html5QrcodeScanType.SCAN_TYPE_FILE],
                    },
                    false,
                );

                html5QrCodeScanner.render(onScanSuccess, onScanFailure);
                message.textContent = 'Arahkan kamera ke QR Code atau pilih file jika tidak muncul.';
            } catch (err) {
                message.textContent = 'Tidak dapat memulai scanner: ' + err;
            }
        }

        Html5Qrcode.getCameras()
            .then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    html5QrCode = new Html5Qrcode('reader');
                    html5QrCode.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 280, height: 280 } },
                        onScanSuccess,
                        onScanFailure,
                    ).then(() => {
                        message.textContent = 'Arahkan kamera ke QR Code siswa.';
                    }).catch(err => {
                        message.textContent = 'Kamera ditemukan tapi gagal dibuka: ' + err + '. Menggunakan fallback scan.';
                        startScanner();
                    });
                    return;
                }

                startScanner();
            })
            .catch(err => {
                message.textContent = 'Gagal memeriksa kamera: ' + err + '. Menggunakan fallback scan.';
                startScanner();
            });
    </script>
</body>
</html>
