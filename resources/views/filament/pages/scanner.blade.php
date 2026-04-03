<x-filament-panels::page>
    <style>
        /* ── Filament CSS Variable Mapping ── */
        .scanner-wrap {
            --sc-bg:        rgb(var(--gray-50));
            --sc-card:      rgb(var(--gray-100));
            --sc-border:    rgb(var(--gray-200));
            --sc-text:      rgb(var(--gray-900));
            --sc-muted:     rgb(var(--gray-500));
            --sc-input-bg:  rgb(var(--gray-50));
            --sc-radius:    0.875rem;
            --sc-primary:   rgb(var(--primary-600));
            --sc-primary-h: rgb(var(--primary-700));
        }

        .dark .scanner-wrap {
            --sc-bg:        rgb(var(--gray-950));
            --sc-card:      rgb(var(--gray-900));
            --sc-border:    rgb(var(--gray-700));
            --sc-text:      rgb(var(--gray-100));
            --sc-muted:     rgb(var(--gray-400));
            --sc-input-bg:  rgb(var(--gray-800));
        }

        .scanner-wrap {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            color: var(--sc-text);
        }

        /* ── Scan Ready Bar ── */
        .sc-ready-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border: 1.5px solid;
            border-radius: var(--sc-radius);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .sc-ready-bar.ready     { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
        .sc-ready-bar.buffering { background: #eff6ff; border-color: #93c5fd; color: #1e40af; animation: sc-blink .5s ease-in-out infinite; }
        .sc-ready-bar.lost      { background: #fef9c3; border-color: #fde047; color: #854d0e; }
        .dark .sc-ready-bar.ready     { background: #052e16; border-color: #166534; color: #86efac; }
        .dark .sc-ready-bar.buffering { background: #0c1a3d; border-color: #1e40af; color: #93c5fd; }
        .dark .sc-ready-bar.lost      { background: #2d1f00; border-color: #854d0e; color: #fde68a; }

        @keyframes sc-blink { 0%,100%{opacity:1} 50%{opacity:.55} }

        .sc-pulse-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            background: currentColor;
            animation: sc-pulse 1.2s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes sc-pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

        /* ── Two Column Layout ── */
        .sc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .sc-grid { grid-template-columns: 1fr; }
        }

        /* ── Card ── */
        .sc-card {
            background: var(--sc-card);
            border: 1px solid var(--sc-border);
            border-radius: var(--sc-radius);
            overflow: hidden;
        }
        .sc-card-header {
            padding: 14px 18px 10px;
            border-bottom: 1px solid var(--sc-border);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sc-card-header span {
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--sc-muted);
        }
        .sc-card-body { padding: 16px 18px; }

        /* ── Camera Area ── */
        .sc-camera-wrap {
            width: 100%;
            aspect-ratio: 4/3;
            border-radius: calc(var(--sc-radius) - 2px);
            overflow: hidden;
            background: #0f172a;
        }
        #reader { width: 100%; height: 100%; }

        /* ── Status Bar ── */
        .sc-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: calc(var(--sc-radius) - 2px);
            background: var(--sc-bg);
            border: 1px solid var(--sc-border);
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--sc-muted);
            transition: background 0.2s, color 0.2s;
            margin-top: 10px;
        }
        .sc-status.loading { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
        .dark .sc-status.loading { background: #0c1a3d; color: #93c5fd; border-color: #1e3a8a; }
        .sc-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: currentColor;
            animation: sc-pulse 1.2s ease-in-out infinite;
            flex-shrink: 0;
        }

        /* ── Result Card ── */
        #sc-result {
            display: none;
            border-radius: calc(var(--sc-radius) - 2px);
            overflow: hidden;
            border: 1px solid transparent;
        }
        #sc-result.show { display: block; animation: sc-slide 0.2s ease; }
        @keyframes sc-slide {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .sc-result-head {
            padding: 14px 18px;
            display: flex; align-items: center; gap: 12px;
        }
        .sc-result-icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; flex-shrink: 0;
        }
        .sc-result-head .sc-label { font-weight: 700; font-size: 0.9375rem; }
        .sc-result-head .sc-sub   { font-size: 0.8rem; margin-top: 2px; opacity: 0.75; }
        .sc-result-body {
            padding: 12px 18px 16px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px;
        }
        .sc-info-key { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; opacity: 0.5; margin-bottom: 2px; }
        .sc-info-val { font-size: 0.9rem; font-weight: 600; }

        #sc-result.success { border-color: #bbf7d0; }
        #sc-result.success .sc-result-head { background: #ecfdf5; color: #166534; }
        #sc-result.success .sc-result-icon { background: #bbf7d0; color: #15803d; }
        #sc-result.success .sc-result-body { background: #f0fdf4; color: #14532d; }
        .dark #sc-result.success { border-color: #166534; }
        .dark #sc-result.success .sc-result-head { background: #052e16; color: #86efac; }
        .dark #sc-result.success .sc-result-icon { background: #14532d; color: #86efac; }
        .dark #sc-result.success .sc-result-body { background: #031a0e; color: #bbf7d0; }

        #sc-result.warning { border-color: #fde68a; }
        #sc-result.warning .sc-result-head { background: #fffbeb; color: #92400e; }
        #sc-result.warning .sc-result-icon { background: #fde68a; color: #b45309; }
        #sc-result.warning .sc-result-body { background: #fffdf5; color: #78350f; }
        .dark #sc-result.warning { border-color: #92400e; }
        .dark #sc-result.warning .sc-result-head { background: #2d1f00; color: #fde68a; }
        .dark #sc-result.warning .sc-result-icon { background: #451a03; color: #fde68a; }
        .dark #sc-result.warning .sc-result-body { background: #1c1000; color: #fef3c7; }

        #sc-result.error { border-color: #fecaca; }
        #sc-result.error .sc-result-head { background: #fef2f2; color: #991b1b; }
        #sc-result.error .sc-result-icon { background: #fecaca; color: #b91c1c; }
        .dark #sc-result.error { border-color: #991b1b; }
        .dark #sc-result.error .sc-result-head { background: #2d0a0a; color: #fca5a5; }
        .dark #sc-result.error .sc-result-icon { background: #450a0a; color: #fca5a5; }

        /* ── Manual Input ── */
        .sc-manual-row { display: flex; gap: 8px; }
        .sc-manual-input {
            flex: 1; padding: 11px 14px;
            border-radius: calc(var(--sc-radius) - 2px);
            border: 1.5px solid var(--sc-border);
            font-size: 0.9375rem; font-family: inherit;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s;
            background: var(--sc-input-bg); color: var(--sc-text);
        }
        .sc-manual-input::placeholder { color: var(--sc-muted); }
        .sc-manual-input:focus {
            border-color: var(--sc-primary);
            box-shadow: 0 0 0 3px rgb(var(--primary-500) / 0.15);
        }
        .sc-manual-btn {
            padding: 11px 20px; border-radius: calc(var(--sc-radius) - 2px);
            border: none; background: var(--sc-primary); color: white;
            font-size: 0.9rem; font-weight: 600; font-family: inherit;
            cursor: pointer; transition: background 0.15s, transform 0.1s; white-space: nowrap;
        }
        .sc-manual-btn:hover   { background: var(--sc-primary-h); }
        .sc-manual-btn:active  { transform: scale(0.97); }
        .sc-manual-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* ── Stats Row ── */
        .sc-stats {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;
        }
        @media (max-width: 480px) { .sc-stats { grid-template-columns: 1fr; } }
        .sc-stat {
            background: var(--sc-card); border: 1px solid var(--sc-border);
            border-radius: var(--sc-radius); padding: 14px 16px;
        }
        .sc-stat-label {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: var(--sc-muted); margin-bottom: 4px;
        }
        .sc-stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
        .sc-stat-value.blue  { color: rgb(var(--primary-600)); }
        .sc-stat-value.green { color: #16a34a; }
        .sc-stat-value.red   { color: #dc2626; }

        .sc-icon { width: 14px; height: 14px; opacity: 0.6; }

        #barcode-trap {
            position: fixed; top: -9999px; left: -9999px;
            width: 1px; height: 1px; opacity: 0; pointer-events: none;
        }

        /* ── Override html5-qrcode UI ── */
            #reader { border: none !important; padding: 0 !important; }
            #reader__scan_region { border-radius: calc(var(--sc-radius) - 2px) !important; overflow: hidden !important; }
            #reader__scan_region video { border-radius: calc(var(--sc-radius) - 2px) !important; object-fit: cover !important; width: 100% !important; height: 100% !important; }
            #reader__scan_region img { display: none !important; }

            /* Sembunyikan header bawaan (teks "Html5Qrcode" dll) */
            #reader__header_message { display: none !important; }
            #reader__status_span { display: none !important; }

            /* Tombol "Start Scanning" bawaan */
            #reader__dashboard_section_csr button {
                background: var(--sc-primary) !important;
                color: white !important;
                border: none !important;
                border-radius: calc(var(--sc-radius) - 2px) !important;
                padding: 10px 20px !important;
                font-weight: 600 !important;
                font-family: inherit !important;
                cursor: pointer !important;
                width: 100% !important;
                margin-top: 8px !important;
            }
            #reader__dashboard_section_csr select {
                border-radius: calc(var(--sc-radius) - 2px) !important;
                border: 1.5px solid var(--sc-border) !important;
                padding: 8px 12px !important;
                background: var(--sc-input-bg) !important;
                color: var(--sc-text) !important;
                font-family: inherit !important;
                width: 100% !important;
            }
            #reader__dashboard_section_fsr span,
            #reader__filescan_input { display: none !important; }

            /* Tombol stop */
            #reader__dashboard_section_swaplink {
                color: var(--sc-muted) !important;
                font-size: 0.75rem !important;
            }

            /* QR box overlay — bingkai scan */
            #reader__scan_region canvas {
                border-radius: 8px !important;
            }
    </style>

    <input id="barcode-trap" type="text" autocomplete="off" autocorrect="off"
           spellcheck="false" tabindex="-1" aria-hidden="true">

    <div class="scanner-wrap">

        {{-- Stats --}}
        {{--  --}}

        {{-- Ready Bar --}}
        <div class="sc-ready-bar ready" id="scan-ready-bar">
            <span class="sc-pulse-dot"></span>
            <span id="scan-ready-text">Siap scan — arahkan alat barcode ke halaman ini</span>
        </div>

        {{-- Main Grid --}}
        <div class="sc-grid">

            {{-- Kiri: Kamera --}}
            <div class="sc-card">
                <div class="sc-card-header">
                    <svg class="sc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 10.07 4h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 18.07 7H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3"/></svg>
                    <span>Kamera QR</span>
                </div>
                <div class="sc-card-body">
                    <div class="sc-camera-wrap">
                        <div id="reader"></div>
                    </div>
                    <div class="sc-status" id="status-bar">
                        <span class="sc-dot"></span>
                        <span id="status-text">Menunggu scan…</span>
                    </div>
                </div>
            </div>

            {{-- Kanan: Hasil + Manual --}}
            <div style="display: flex; flex-direction: column; gap: 1rem;">

                <div class="sc-card">
                    <div class="sc-card-header">
                        <svg class="sc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        <span>Hasil Scan</span>
                    </div>
                    <div class="sc-card-body" style="padding-top:10px; padding-bottom:10px;">
                        <div id="sc-result">
                            <div class="sc-result-head">
                                <div class="sc-result-icon" id="sc-res-icon"></div>
                                <div>
                                    <div class="sc-label" id="sc-res-label"></div>
                                    <div class="sc-sub"   id="sc-res-sub"></div>
                                </div>
                            </div>
                            <div class="sc-result-body" id="sc-res-body"></div>
                        </div>
                        <div id="sc-placeholder" style="padding:28px 0; text-align:center; color:var(--sc-muted); font-size:0.875rem;">
                            Hasil scan akan muncul di sini
                        </div>
                    </div>
                </div>

                <div class="sc-card">
                    <div class="sc-card-header">
                        <svg class="sc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        <span>Input NISN Manual</span>
                    </div>
                    <div class="sc-card-body">
                        <div class="sc-manual-row">
                            <input id="manual-nisn" class="sc-manual-input" type="text"
                                   placeholder="Contoh: 0012345678" autocomplete="off">
                            <button id="manual-btn" class="sc-manual-btn" type="button">Catat</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script>
        const API_URL      = '{{ route('scanner.api') }}';
        const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
        const SCAN_TIMEOUT = 200;
        const MIN_LENGTH   = 3;

        const trap         = document.getElementById('barcode-trap');
        const statusBar    = document.getElementById('status-bar');
        const statusText   = document.getElementById('status-text');
        const scResult     = document.getElementById('sc-result');
        const scPlaceholder= document.getElementById('sc-placeholder');
        const manualBtn    = document.getElementById('manual-btn');
        const manualInput  = document.getElementById('manual-nisn');
        const scanReadyBar = document.getElementById('scan-ready-bar');
        const scanReadyTxt = document.getElementById('scan-ready-text');

        let scanTimer = null;
        let isSending = false;

        // ── Stats ─────────────────────────────────────────────
        async function loadStats() {
            try {
                const res  = await fetch('{{ route("scanner.stats") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                const data = await res.json();
                document.getElementById('stat-total').textContent  = data.total ?? '—';
                document.getElementById('stat-hadir').textContent  = data.hadir ?? '—';
                document.getElementById('stat-belum').textContent  = data.belum ?? '—';
            } catch (_) {}
        }
        loadStats();

        // ── Focus ─────────────────────────────────────────────
        function refocusTrap() {
            if (document.activeElement === manualInput) return;
            trap.focus();
        }
        setInterval(() => {
            if (document.activeElement !== manualInput && document.activeElement !== trap) trap.focus();
        }, 800);
        window.addEventListener('load', () => setTimeout(refocusTrap, 200));
        document.addEventListener('click', (e) => {
            if (e.target !== manualInput && !manualInput.contains(e.target)) setTimeout(refocusTrap, 50);
        });
        manualInput.addEventListener('blur', () => setTimeout(refocusTrap, 150));
        trap.addEventListener('focus', () => setScanReady('ready'));
        trap.addEventListener('blur',  () => {
            if (document.activeElement !== manualInput) setScanReady('lost');
        });

        // ── Helpers ───────────────────────────────────────────
        function setStatus(text, type = 'idle') {
            statusText.textContent = text;
            statusBar.className    = 'sc-status' + (type === 'loading' ? ' loading' : '');
        }
        function setScanReady(state) {
            scanReadyBar.className = 'sc-ready-bar ' + state;
            const msgs = { ready: 'Siap scan — arahkan alat barcode ke halaman ini', buffering: '⚡ Menerima data scanner…', lost: 'Klik area ini agar alat scan bisa terbaca' };
            scanReadyTxt.textContent = msgs[state] ?? '';
        }
        function getNisn(raw) {
            try { const u = new URL(raw); if (u.searchParams.has('nisn')) return u.searchParams.get('nisn'); } catch (_) {}
            return raw.trim();
        }

        function showResult(data) {
            scPlaceholder.style.display = 'none';
            scResult.classList.remove('show', 'success', 'warning', 'error');

            let cls, icon, label;
            if (data.success)       { cls = 'success'; icon = '✓'; label = 'Berhasil Dicatat!'; }
            else if (data.student)  { cls = 'warning'; icon = '!'; label = 'Sudah Ambil Hari Ini'; }
            else                    { cls = 'error';   icon = '✕'; label = 'Tidak Ditemukan'; }

            document.getElementById('sc-res-icon').textContent  = icon;
            document.getElementById('sc-res-label').textContent = label;
            document.getElementById('sc-res-sub').textContent   = data.message;

            const body = document.getElementById('sc-res-body');
            if (data.student) {
                const s = data.student;
                body.style.display = 'grid';
                body.innerHTML = `
                    <div><div class="sc-info-key">Nama</div><div class="sc-info-val">${s.name}</div></div>
                    <div><div class="sc-info-key">NISN</div><div class="sc-info-val">${s.nisn}</div></div>
                    <div><div class="sc-info-key">Kelas</div><div class="sc-info-val">${s.kelas}</div></div>
                    <div><div class="sc-info-key">Jurusan</div><div class="sc-info-val">${s.jurusan}</div></div>
                    ${s.waktu ? `<div style="grid-column:span 2"><div class="sc-info-key">Waktu Ambil</div><div class="sc-info-val"> ${s.waktu}</div></div>` : ''}
                `;
            } else {
                body.style.display = 'none';
                body.innerHTML = '';
            }

            scResult.className = cls;
            void scResult.offsetWidth;
            scResult.classList.add('show');
        }

        // ── Submit ────────────────────────────────────────────
        async function submitNisn(nisn) {
            if (isSending || !nisn) return;
            isSending = true; manualBtn.disabled = true;
            setStatus('Memproses ' + nisn + '…', 'loading');
            setScanReady('ready');
            try {
                const res  = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ nisn }),
                });
                const data = await res.json();
                showResult(data);
                setStatus(data.success ? '✓ Berhasil — siap scan berikutnya.' : '⚠ ' + data.message);
                if (data.success) loadStats();
            } catch (_) {
                showResult({ success: false, message: 'Gagal terhubung ke server.', student: null });
                setStatus('Koneksi gagal. Coba lagi.');
            } finally {
                isSending = false; manualBtn.disabled = false;
                manualInput.value = ''; trap.value = '';
                refocusTrap();
            }
        }

        // ── Trap ─────────────────────────────────────────────
        trap.addEventListener('input', () => {
            if (!trap.value) return;
            setScanReady('buffering'); clearTimeout(scanTimer);
            scanTimer = setTimeout(() => {
                const raw = trap.value.trim(); trap.value = '';
                if (raw.length >= MIN_LENGTH) submitNisn(getNisn(raw)); else setScanReady('ready');
            }, SCAN_TIMEOUT);
        });
        trap.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault(); clearTimeout(scanTimer);
            const raw = trap.value.trim(); trap.value = '';
            if (raw.length >= MIN_LENGTH) submitNisn(getNisn(raw));
        });

        // ── Manual ───────────────────────────────────────────
        manualBtn.addEventListener('click', () => {
            const nisn = manualInput.value.trim();
            if (!nisn) { manualInput.focus(); return; }
            submitNisn(nisn);
        });
        manualInput.addEventListener('keydown', e => { if (e.key === 'Enter') manualBtn.click(); });

        // ── Kamera ───────────────────────────────────────────
        let html5QrCode = null, html5QrCodeScanner = null;
        function onScanSuccess(decodedText) { if (decodedText) submitNisn(getNisn(decodedText)); }
        function startFallbackScanner() {
            try {
                html5QrCodeScanner = new Html5QrcodeScanner('reader', {
                    fps: 10, qrbox: { width: 220, height: 220 },
                    rememberLastUsedCamera: true, showTorchButtonIfSupported: true,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA, Html5QrcodeScanType.SCAN_TYPE_FILE],
                }, false);
                html5QrCodeScanner.render(onScanSuccess, () => {});
                setStatus('Arahkan kamera ke QR Code atau pilih file.');
            } catch (_) { setStatus('Kamera tidak tersedia. Gunakan alat scan atau input manual.'); }
        }
        Html5Qrcode.getCameras()
        .then(cameras => {
            if (cameras && cameras.length) {
                // Pilih kamera belakang kalau ada
                const backCamera = cameras.find(c =>
                    c.label.toLowerCase().includes('back') ||
                    c.label.toLowerCase().includes('rear') ||
                    c.label.toLowerCase().includes('environment')
                ) || cameras[cameras.length - 1]; // fallback kamera terakhir

                html5QrCode = new Html5Qrcode('reader');
                html5QrCode.start(
                    backCamera.id, // ← pakai kamera belakang
                    { fps: 60, qrbox: { width: 220, height: 400 } },
                    onScanSuccess, () => {}
                )
                .then(() => setStatus('Arahkan kamera ke QR Code siswa.'))
                .catch(() => startFallbackScanner());
            } else {
                startFallbackScanner();
            }
        }).catch(() => startFallbackScanner());
    </script>

</x-filament-panels::page>