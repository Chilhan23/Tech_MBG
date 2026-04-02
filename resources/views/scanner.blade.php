<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner MBG</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

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
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 720px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            padding: 28px;
        }

        h1 { margin: 0 0 6px; font-size: 1.5rem; font-weight: 700; }
        .subtitle { color: #6b7280; margin: 0 0 24px; line-height: 1.6; }

        #barcode-trap {
            position: fixed;
            top: -9999px;
            left: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .scan-ready {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1.5px solid;
            border-radius: 14px;
            margin-bottom: 18px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .scan-ready.ready     { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
        .scan-ready.buffering { background: #eff6ff; border-color: #93c5fd; color: #1e40af; animation: blink .5s ease-in-out infinite; }
        .scan-ready.lost      { background: #fef9c3; border-color: #fde047; color: #854d0e; }

        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.6} }

        .scan-ready .pulse-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse 1.2s ease-in-out infinite;
            flex-shrink: 0;
        }

        .scanner-area {
            width: 100%;
            min-height: 320px;
            border-radius: 18px;
            overflow: hidden;
            background: #111827;
        }

        .status-bar {
            margin-top: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            background: #f9fafb;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            min-height: 44px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }

        #result-card {
            margin-top: 20px;
            border-radius: 16px;
            overflow: hidden;
            display: none;
        }
        #result-card.show {
            display: block;
            animation: slideIn 0.25s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            padding: 16px 20px 14px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .result-icon {
            width: 44px; height: 44px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 700; flex-shrink: 0;
        }
        .result-header .text-wrap { flex: 1; }
        .result-header .label { font-weight: 700; font-size: 1rem; }
        .result-header .sub   { font-size: 0.8125rem; margin-top: 2px; opacity: 0.8; }

        .result-body {
            padding: 14px 20px 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 24px;
        }
        .info-item .key {
            font-size: 0.72rem; font-weight: 600;
            letter-spacing: 0.06em; text-transform: uppercase;
            opacity: 0.55; margin-bottom: 3px;
        }
        .info-item .val { font-size: 0.9375rem; font-weight: 600; }

        .result-card-success .result-header { background: #ecfdf5; color: #166534; }
        .result-card-success .result-icon   { background: #bbf7d0; color: #15803d; }
        .result-card-success .result-body   { background: #f0fdf4; color: #14532d; }

        .result-card-warning .result-header { background: #fffbeb; color: #92400e; }
        .result-card-warning .result-icon   { background: #fde68a; color: #b45309; }
        .result-card-warning .result-body   { background: #fffdf0; color: #78350f; }

        .result-card-error .result-header   { background: #fef2f2; color: #991b1b; }
        .result-card-error .result-icon     { background: #fecaca; color: #b91c1c; }

        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }

        .manual-section label {
            display: block; font-size: 0.875rem;
            font-weight: 600; color: #374151; margin-bottom: 8px;
        }
        .manual-row { display: flex; gap: 10px; }
        .manual-row input {
            flex: 1; padding: 13px 16px;
            border-radius: 12px; border: 1.5px solid #d1d5db;
            font-size: 1rem; font-family: inherit;
            outline: none; transition: border-color 0.15s;
        }
        .manual-row input:focus { border-color: #2563eb; }
        .manual-row button {
            padding: 13px 22px; border-radius: 12px; border: none;
            background: #2563eb; color: white; font-size: 0.9375rem;
            font-weight: 600; font-family: inherit; cursor: pointer;
            transition: background 0.15s; white-space: nowrap;
        }
        .manual-row button:hover    { background: #1d4ed8; }
        .manual-row button:active   { background: #1e40af; }
        .manual-row button:disabled { background: #93c5fd; cursor: not-allowed; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.35; }
        }
        .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: currentColor;
            animation: pulse 1.2s ease-in-out infinite;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<input id="barcode-trap" type="text" autocomplete="off" autocorrect="off"
       spellcheck="false" tabindex="-1" aria-hidden="true">

<div class="page">
    <div class="card">
        <h1>Scanner QR MBG</h1>

        <div class="scan-ready ready" id="scan-ready-bar">
            <span class="pulse-dot"></span>
            <span id="scan-ready-text">Siap scan — arahkan alat barcode ke halaman ini</span>
        </div>

        <p class="subtitle">
            Scan dengan alat barcode, arahkan kamera ke QR Code, atau input NISN manual.
        </p>

        <div id="reader" class="scanner-area"></div>

        <div class="status-bar" id="status-bar">
            <span class="dot"></span>
            <span id="status-text">Menunggu scan…</span>
        </div>

        <div id="result-card"></div>

        <hr class="divider">

        <div class="manual-section">
            <label for="manual-nisn">Input NISN manual</label>
            <div class="manual-row">
                <input id="manual-nisn" type="text" placeholder="Contoh: 0012345678" autocomplete="off">
                <button id="manual-btn" type="button">Catat Absensi</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.7/html5-qrcode.min.js"></script>
<script>
    const API_URL      = '{{ route('scanner.api') }}';
    const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
    const SCAN_TIMEOUT = 200;
    const MIN_LENGTH   = 3;

    const trap         = document.getElementById('barcode-trap');
    const statusBar    = document.getElementById('status-bar');
    const statusText   = document.getElementById('status-text');
    const resultCard   = document.getElementById('result-card');
    const manualBtn    = document.getElementById('manual-btn');
    const manualInput  = document.getElementById('manual-nisn');
    const scanReadyBar = document.getElementById('scan-ready-bar');
    const scanReadyTxt = document.getElementById('scan-ready-text');

    let scanTimer = null;
    let isSending = false;

    // ── Focus management ─────────────────────────────────────
    function refocusTrap() {
        if (document.activeElement === manualInput) return;
        trap.focus();
    }

    setInterval(() => {
        if (document.activeElement !== manualInput && document.activeElement !== trap) {
            trap.focus();
        }
    }, 800);

    window.addEventListener('load', () => setTimeout(refocusTrap, 200));

    document.addEventListener('click', (e) => {
        if (e.target !== manualInput && !manualInput.contains(e.target)) {
            setTimeout(refocusTrap, 50);
        }
    });

    manualInput.addEventListener('blur', () => setTimeout(refocusTrap, 150));

    trap.addEventListener('focus', () => setScanReady('ready'));
    trap.addEventListener('blur',  () => {
        if (document.activeElement !== manualInput) setScanReady('lost');
    });

    // ── Helpers ──────────────────────────────────────────────
    function setStatus(text, type = 'idle') {
        statusText.textContent     = text;
        statusBar.style.background = type === 'loading' ? '#eff6ff' : '';
        statusBar.style.color      = type === 'loading' ? '#1d4ed8' : '';
    }

    function setScanReady(state) {
        scanReadyBar.className = 'scan-ready ' + state;
        if (state === 'ready') {
            scanReadyTxt.textContent = 'Siap scan — arahkan alat barcode ke halaman ini';
        } else if (state === 'buffering') {
            scanReadyTxt.textContent = '⚡ Menerima data scanner…';
        } else {
            scanReadyTxt.textContent = 'Klik area ini agar alat scan bisa terbaca';
        }
    }

    function getNisn(raw) {
        try {
            const url = new URL(raw);
            if (url.searchParams.has('nisn')) return url.searchParams.get('nisn');
        } catch (_) {}
        return raw.trim();
    }

    function showResult(data) {
        let cls, icon, label, body = '';

        if (data.success) {
            cls = 'result-card-success'; icon = '✓'; label = 'Berhasil Dicatat!';
        } else if (data.student) {
            cls = 'result-card-warning'; icon = '!'; label = 'Sudah Ambil Hari Ini';
        } else {
            cls = 'result-card-error';   icon = '✕'; label = 'Tidak Ditemukan';
        }

        if (data.student) {
            const s = data.student;
            body = `
                <div class="result-body">
                    <div class="info-item"><div class="key">Nama</div><div class="val">${s.name}</div></div>
                    <div class="info-item"><div class="key">NISN</div><div class="val">${s.nisn}</div></div>
                    <div class="info-item"><div class="key">Kelas</div><div class="val">${s.kelas}</div></div>
                    <div class="info-item"><div class="key">Jurusan</div><div class="val">${s.jurusan}</div></div>
                </div>`;
        }

        resultCard.className = cls;
        resultCard.innerHTML = `
            <div class="result-header">
                <div class="result-icon">${icon}</div>
                <div class="text-wrap">
                    <div class="label">${label}</div>
                    <div class="sub">${data.message}</div>
                </div>
            </div>${body}`;

        resultCard.classList.remove('show');
        void resultCard.offsetWidth;
        resultCard.classList.add('show');
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── Submit ────────────────────────────────────────────────
    async function submitNisn(nisn) {
        if (isSending || !nisn) return;
        isSending = true;
        manualBtn.disabled = true;
        setStatus('Memproses ' + nisn + '…', 'loading');
        setScanReady('ready');

        try {
            const res  = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ nisn }),
            });
            const data = await res.json();
            showResult(data);
            setStatus(data.success ? '✓ Berhasil — siap scan berikutnya.' : '⚠ ' + data.message);
        } catch (_) {
            showResult({ success: false, message: 'Gagal terhubung ke server.', student: null });
            setStatus('Koneksi gagal. Coba lagi.');
        } finally {
            isSending          = false;
            manualBtn.disabled = false;
            manualInput.value  = '';
            trap.value         = '';
            refocusTrap();
        }
    }

    // ── Trap input handler ────────────────────────────────────
    trap.addEventListener('input', () => {
        const val = trap.value;
        if (!val) return;

        setScanReady('buffering');
        clearTimeout(scanTimer);

        scanTimer = setTimeout(() => {
            const raw = trap.value.trim();
            trap.value = '';
            if (raw.length >= MIN_LENGTH) {
                submitNisn(getNisn(raw));
            } else {
                setScanReady('ready');
            }
        }, SCAN_TIMEOUT);
    });

    trap.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(scanTimer);
            const raw = trap.value.trim();
            trap.value = '';
            if (raw.length >= MIN_LENGTH) submitNisn(getNisn(raw));
        }
    });

    // ── Manual input ──────────────────────────────────────────
    manualBtn.addEventListener('click', () => {
        const nisn = manualInput.value.trim();
        if (!nisn) { manualInput.focus(); return; }
        submitNisn(nisn);
    });

    manualInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') manualBtn.click();
    });

    // ── Kamera QR ─────────────────────────────────────────────
    let html5QrCode = null;
    let html5QrCodeScanner = null;

    function onScanSuccess(decodedText) {
        if (!decodedText) return;
        submitNisn(getNisn(decodedText));
    }

    function startFallbackScanner() {
        try {
            html5QrCodeScanner = new Html5QrcodeScanner('reader', {
                fps: 10,
                qrbox: { width: 280, height: 280 },
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                supportedScanTypes: [
                    Html5QrcodeScanType.SCAN_TYPE_CAMERA,
                    Html5QrcodeScanType.SCAN_TYPE_FILE,
                ],
            }, false);
            html5QrCodeScanner.render(onScanSuccess, () => {});
            setStatus('Arahkan kamera ke QR Code atau pilih file.');
        } catch (_) {
            setStatus('Kamera tidak tersedia. Gunakan alat scan atau input manual.');
        }
    }

    Html5Qrcode.getCameras()
        .then(cameras => {
            if (cameras && cameras.length) {
                html5QrCode = new Html5Qrcode('reader');
                html5QrCode.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 280, height: 280 } },
                    onScanSuccess,
                    () => {}
                ).then(() => {
                    setStatus('Arahkan kamera ke QR Code siswa.');
                }).catch(() => startFallbackScanner());
            } else {
                startFallbackScanner();
            }
        })
        .catch(() => startFallbackScanner());
</script>
</body>
</html>