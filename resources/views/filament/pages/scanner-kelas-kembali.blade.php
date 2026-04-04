<x-filament-panels::page>
{{-- CSS sama persis, ganti warna accent jadi merah --}}
<style>
    /* copy semua CSS dari scanner siswa, ganti: */
    .scanner-wrap {
        --c-bg:         #f8f7f5;
        --c-surface:    #ffffff;
        --c-surface-2:  #f2f1ef;
        --c-border:     #e8e6e2;
        --c-border-2:   #d4d0ca;
        --c-text:       #1a1916;
        --c-text-2:     #6b6860;
        --c-text-3:     #9b9890;
        --c-accent:     #7c3aed;
        --c-accent-2:   #6d28d9;
        --c-green:      #16a34a;
        --c-green-bg:   #f0fdf4;
        --c-green-bdr:  #bbf7d0;
        --c-amber:      #d97706;
        --c-amber-bg:   #fffbeb;
        --c-amber-bdr:  #fde68a;
        --c-red:        #dc2626;
        --c-red-bg:     #fef2f2;
        --c-red-bdr:    #fecaca;
        --c-blue-bg:    #eff6ff;
        --c-blue-bdr:   #bfdbfe;
        --radius:       14px;
        --radius-sm:    8px;
        --shadow-sm:    0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md:    0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
        --shadow-lg:    0 12px 32px rgba(0,0,0,.1), 0 4px 8px rgba(0,0,0,.06);
        --font:         'Plus Jakarta Sans', system-ui, sans-serif;
        --font-mono:    'JetBrains Mono', monospace;
    }
    .dark .scanner-wrap {
            --c-bg:         #111110;
            --c-surface:    #1c1b19;
            --c-surface-2:  #252420;
            --c-border:     #2e2d29;
            --c-border-2:   #3d3c37;
            --c-text:       #f5f4f0;
            --c-text-2:     #a8a59e;
            --c-text-3:     #706e67;
            --c-green-bg:   #052e16;
            --c-green-bdr:  #166534;
            --c-amber-bg:   #2d1f00;
            --c-amber-bdr:  #92400e;
            --c-red-bg:     #2d0a0a;
            --c-red-bdr:    #991b1b;
            --c-blue-bg:    #0c1a3d;
            --c-blue-bdr:   #1e3a8a;
        }

        /* ── Reset ── */
        .scanner-wrap * { box-sizing: border-box; }

        .scanner-wrap {
            font-family: var(--font);
            color: var(--c-text);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── Page Header ── */
        .sc-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1.5rem 1.75rem;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            gap: 1rem;
        }
        .sc-page-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--c-text);
            line-height: 1.15;
            margin-bottom: 4px;
        }
        .sc-page-subtitle {
            font-size: 0.875rem;
            color: var(--c-text-2);
            font-weight: 500;
        }
        .sc-page-date {
            font-family: var(--font-mono);
            font-size: 0.78rem;
            color: var(--c-text-3);
            background: var(--c-surface-2);
            border: 1px solid var(--c-border);
            padding: 6px 12px;
            border-radius: 100px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── Stats Row ── */
        .sc-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.875rem;
        }
        @media (max-width: 600px) { .sc-stats { grid-template-columns: 1fr; } }

        .sc-stat {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .sc-stat:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
        .sc-stat::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: var(--radius) var(--radius) 0 0;
        }
        .sc-stat.blue::before  { background: linear-gradient(90deg, #2563eb, #60a5fa); }
        .sc-stat.green::before { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .sc-stat.red::before   { background: linear-gradient(90deg, #dc2626, #f87171); }

        .sc-stat-emoji { font-size: 1.4rem; margin-bottom: 10px; display: block; }
        .sc-stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--c-text-3);
            margin-bottom: 6px;
        }
        .sc-stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .sc-stat.blue  .sc-stat-value { color: #2563eb; }
        .sc-stat.green .sc-stat-value { color: #16a34a; }
        .sc-stat.red   .sc-stat-value { color: #dc2626; }

        /* ── Ready Bar ── */
        .sc-ready-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: var(--radius);
            border: 1.5px solid;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.25s;
        }
        .sc-ready-bar.ready     { background: var(--c-green-bg); border-color: var(--c-green-bdr); color: #166534; }
        .sc-ready-bar.buffering { background: var(--c-blue-bg);  border-color: var(--c-blue-bdr);  color: #1e40af; animation: sc-blink .5s ease-in-out infinite; }
        .sc-ready-bar.lost      { background: var(--c-amber-bg); border-color: var(--c-amber-bdr); color: #854d0e; }
        .dark .sc-ready-bar.ready     { color: #86efac; }
        .dark .sc-ready-bar.buffering { color: #93c5fd; }
        .dark .sc-ready-bar.lost      { color: #fde68a; }

        @keyframes sc-blink { 0%,100%{opacity:1} 50%{opacity:.5} }

        .sc-pulse-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: currentColor;
            animation: sc-pulse 1.4s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes sc-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.35;transform:scale(.8)} }

        .sc-ready-icon { font-size: 1rem; flex-shrink: 0; }

        /* ── Two-column Grid ── */
        .sc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            align-items: start;
        }
        @media (max-width: 860px) { .sc-grid { grid-template-columns: 1fr; } }

        /* ── Card ── */
        .sc-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .sc-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--c-border);
            background: var(--c-surface-2);
        }
        .sc-card-header-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
        }
        .sc-card-header-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--c-text-2);
        }
        .sc-card-body { padding: 18px 20px; }

        /* ── Camera ── */
        .sc-camera-wrap {
            width: 100%;
            aspect-ratio: 4/3;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: #0a0a0a;
            position: relative;
        }
        .sc-camera-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
        }
        .sc-camera-corner {
            position: absolute;
            width: 20px; height: 20px;
            border-color: rgba(255,255,255,.5);
            border-style: solid;
        }
        .sc-camera-corner.tl { top: 12px; left: 12px; border-width: 2px 0 0 2px; }
        .sc-camera-corner.tr { top: 12px; right: 12px; border-width: 2px 2px 0 0; }
        .sc-camera-corner.bl { bottom: 12px; left: 12px; border-width: 0 0 2px 2px; }
        .sc-camera-corner.br { bottom: 12px; right: 12px; border-width: 0 2px 2px 0; }
        #reader { width: 100%; height: 100%; }

        /* ── Status Bar ── */
        .sc-status {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            background: var(--c-surface-2);
            border: 1px solid var(--c-border);
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--c-text-2);
            transition: all 0.2s;
        }
        .sc-status.loading {
            background: var(--c-blue-bg);
            border-color: var(--c-blue-bdr);
            color: #1d4ed8;
        }
        .dark .sc-status.loading { color: #93c5fd; }
        .sc-status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor;
            animation: sc-pulse 1.2s ease-in-out infinite;
            flex-shrink: 0;
        }

        /* ── Result Card ── */
        #sc-result {
            display: none;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1.5px solid transparent;
        }
        #sc-result.show { display: block; animation: sc-slide 0.22s cubic-bezier(.22,.68,0,1.2); }
        @keyframes sc-slide {
            from { opacity: 0; transform: translateY(10px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .sc-result-head {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
        }
        .sc-result-icon {
            width: 42px; height: 42px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem;
            font-weight: 900;
            flex-shrink: 0;
        }
        .sc-result-label { font-weight: 700; font-size: 0.9375rem; }
        .sc-result-sub   { font-size: 0.8rem; margin-top: 2px; opacity: 0.7; }

        .sc-result-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
            padding: 12px 16px 14px;
        }
        .sc-info-key {
            font-size: 0.68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            opacity: 0.5; margin-bottom: 2px;
        }
        .sc-info-val {
            font-size: 0.875rem; font-weight: 700;
            font-family: var(--font-mono);
        }

        /* success */
        #sc-result.success { border-color: var(--c-green-bdr); }
        #sc-result.success .sc-result-head { background: var(--c-green-bg); color: #166534; }
        #sc-result.success .sc-result-icon { background: #bbf7d0; color: #15803d; }
        #sc-result.success .sc-result-body { background: #f7fef9; color: #14532d; }
        .dark #sc-result.success .sc-result-head { color: #86efac; }
        .dark #sc-result.success .sc-result-body { background: #031a0e; color: #bbf7d0; }

        /* warning */
        #sc-result.warning { border-color: var(--c-amber-bdr); }
        #sc-result.warning .sc-result-head { background: var(--c-amber-bg); color: #92400e; }
        #sc-result.warning .sc-result-icon { background: #fde68a; color: #b45309; }
        #sc-result.warning .sc-result-body { background: #fffef7; color: #78350f; }
        .dark #sc-result.warning .sc-result-head { color: #fde68a; }
        .dark #sc-result.warning .sc-result-body { background: #1c1000; color: #fef3c7; }

        /* error */
        #sc-result.error { border-color: var(--c-red-bdr); }
        #sc-result.error .sc-result-head { background: var(--c-red-bg); color: #991b1b; }
        #sc-result.error .sc-result-icon { background: #fecaca; color: #b91c1c; }
        .dark #sc-result.error .sc-result-head { color: #fca5a5; }

        /* ── Placeholder ── */
        .sc-placeholder {
            padding: 32px 0;
            text-align: center;
            color: var(--c-text-3);
            font-size: 0.875rem;
        }
        .sc-placeholder-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            opacity: 0.4;
        }

        /* ── Manual Input ── */
        .sc-manual-row {
            display: flex;
            gap: 8px;
        }
        .sc-manual-input {
            flex: 1;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--c-border);
            font-size: 0.9375rem;
            font-family: var(--font-mono);
            font-weight: 500;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: var(--c-bg);
            color: var(--c-text);
        }
        .sc-manual-input::placeholder { color: var(--c-text-3); font-family: var(--font); font-weight: 400; }
        .sc-manual-input:focus {
            border-color: var(--c-accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.15);
        }
        .sc-manual-btn {
            padding: 11px 22px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--c-accent);
            color: white;
            font-size: 0.875rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
            white-space: nowrap;
        }
        .sc-manual-btn:hover   { background: var(--c-accent-2); box-shadow: 0 4px 12px rgba(37,99,235,.35); }
        .sc-manual-btn:active  { transform: scale(0.97); }
        .sc-manual-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ── Divider ── */
        .sc-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--c-text-3);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .sc-divider::before, .sc-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--c-border);
        }

        /* ── Right Column Stack ── */
        .sc-right-col {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── html5-qrcode overrides ── */
        #reader { border: none !important; padding: 0 !important; }
        #reader__scan_region { border-radius: var(--radius-sm) !important; overflow: hidden !important; }
        #reader__scan_region video { border-radius: var(--radius-sm) !important; object-fit: cover !important; width: 100% !important; height: 100% !important; }
        #reader__scan_region img { display: none !important; }
        #reader__header_message { display: none !important; }
        #reader__status_span { display: none !important; }
        #reader__dashboard_section_csr button {
            background: var(--c-accent) !important;
            color: white !important;
            border: none !important;
            border-radius: var(--radius-sm) !important;
            padding: 10px 20px !important;
            font-weight: 700 !important;
            font-family: var(--font) !important;
            cursor: pointer !important;
            width: 100% !important;
            margin-top: 8px !important;
            box-shadow: 0 2px 8px rgba(37,99,235,.25) !important;
        }
        #reader__dashboard_section_csr select {
            border-radius: var(--radius-sm) !important;
            border: 1.5px solid var(--c-border) !important;
            padding: 8px 12px !important;
            background: var(--c-bg) !important;
            color: var(--c-text) !important;
            font-family: var(--font) !important;
            width: 100% !important;
        }
        #reader__dashboard_section_fsr span,
        #reader__filescan_input { display: none !important; }
        #reader__dashboard_section_swaplink { color: var(--c-text-3) !important; font-size: 0.75rem !important; }

        /* ── Hidden Trap ── */
        #barcode-trap {
            position: fixed; top: -9999px; left: -9999px;
            width: 1px; height: 1px; opacity: 0; pointer-events: none;
        }

        /* ── Animations ── */
        .sc-stat { animation: sc-fadein 0.4s ease both; }
        .sc-stat:nth-child(1) { animation-delay: 0.05s; }
        .sc-stat:nth-child(2) { animation-delay: 0.10s; }
        .sc-stat:nth-child(3) { animation-delay: 0.15s; }
        @keyframes sc-fadein { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
</style>

<input id="barcode-trap" type="text" autocomplete="off" autocorrect="off"
       spellcheck="false" tabindex="-1" aria-hidden="true">

<div class="scanner-wrap">
    <div class="sc-page-header">
        <div>
            <div class="sc-page-title">Scanner Kelas — Kembalikan Ompreng</div>
            <div class="sc-page-subtitle">Scan QR Kelas untuk mencatat pengembalian ompreng ke pusat</div>
        </div>
        <div class="sc-page-date" id="sc-live-date">—</div>
    </div>

    <div class="sc-ready-bar ready" id="scan-ready-bar">
        <span class="sc-pulse-dot"></span>
        <span id="scan-ready-text">Siap scan — arahkan alat barcode ke QR Kelas</span>
    </div>

    <div class="sc-grid">
        <div class="sc-card">
            <div class="sc-card-header">
                <span class="sc-card-header-title">Kamera QR Code</span>
            </div>
            <div class="sc-card-body">
                <div class="sc-camera-wrap">
                    <div class="sc-camera-overlay">
                        <div class="sc-camera-corner tl"></div>
                        <div class="sc-camera-corner tr"></div>
                        <div class="sc-camera-corner bl"></div>
                        <div class="sc-camera-corner br"></div>
                    </div>
                    <div id="reader"></div>
                </div>
                <div class="sc-status" id="status-bar">
                    <span class="sc-status-dot"></span>
                    <span id="status-text">Menunggu scan…</span>
                </div>
            </div>
        </div>

        <div class="sc-right-col">
            <div class="sc-card">
                <div class="sc-card-header">
                    <span class="sc-card-header-title">Hasil Scan Kelas</span>
                </div>
                <div class="sc-card-body" style="padding-top:14px;padding-bottom:14px;">
                    <div id="sc-result">
                        <div class="sc-result-head">
                            <div class="sc-result-icon" id="sc-res-icon"></div>
                            <div>
                                <div class="sc-result-label" id="sc-res-label"></div>
                                <div class="sc-result-sub" id="sc-res-sub"></div>
                            </div>
                        </div>
                        <div class="sc-result-body" id="sc-res-body"></div>
                    </div>
                    <div id="sc-placeholder" class="sc-placeholder">
                        <div>Hasil scan kelas akan muncul di sini</div>
                    </div>
                </div>
            </div>

            <div class="sc-card">
                <div class="sc-card-header">
                    <span class="sc-card-header-title">Input Nama Kelas Manual</span>
                </div>
                <div class="sc-card-body">
                    <div class="sc-manual-row">
                        <input id="manual-kelas" class="sc-manual-input" type="text"
                               placeholder="Contoh: 11 RPL 1" autocomplete="off">
                        <button id="manual-btn" class="sc-manual-btn" type="button">Catat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateDate() {
        const now = new Date();
        document.getElementById('sc-live-date').textContent =
            now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    }
    updateDate();
    setInterval(updateDate, 60000);
</script>

<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    const API_URL      = '{{ route('scanner.api.kelas') }}';
    const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
    const SCAN_TIMEOUT = 200;
    const MIN_LENGTH   = 3;

    const trap          = document.getElementById('barcode-trap');
    const statusBar     = document.getElementById('status-bar');
    const statusText    = document.getElementById('status-text');
    const scResult      = document.getElementById('sc-result');
    const scPlaceholder = document.getElementById('sc-placeholder');
    const manualBtn     = document.getElementById('manual-btn');
    const manualInput   = document.getElementById('manual-kelas');
    const scanReadyBar  = document.getElementById('scan-ready-bar');
    const scanReadyTxt  = document.getElementById('scan-ready-text');

    let scanTimer = null;
    let isSending = false;

    function refocusTrap() {
        if (document.activeElement === manualInput) return;
        trap.focus();
    }
    setInterval(() => {
        if (document.activeElement !== manualInput && document.activeElement !== trap) trap.focus();
    }, 800);
    window.addEventListener('load', () => setTimeout(refocusTrap, 200));
    document.addEventListener('click', (e) => {
        if (e.target !== manualInput) setTimeout(refocusTrap, 50);
    });
    manualInput.addEventListener('blur', () => setTimeout(refocusTrap, 150));
    trap.addEventListener('focus', () => setScanReady('ready'));
    trap.addEventListener('blur', () => {
        if (document.activeElement !== manualInput) setScanReady('lost');
    });

    function setStatus(text, type = 'idle') {
        statusText.textContent = text;
        statusBar.className = 'sc-status' + (type === 'loading' ? ' loading' : '');
    }
    function setScanReady(state) {
        scanReadyBar.className = 'sc-ready-bar ' + state;
        const msgs = {
            ready:     'Siap scan — arahkan alat barcode ke QR Kelas',
            buffering: 'Menerima data scanner…',
            lost:      'Klik area ini agar alat scan bisa terbaca'
        };
        scanReadyTxt.textContent = msgs[state] ?? '';
    }

    function showResult(data) {
        scPlaceholder.style.display = 'none';
        scResult.classList.remove('show', 'success', 'warning', 'error');

        let cls, icon, label;
        if (data.success)              { cls = 'success'; icon = '✓'; label = 'Berhasil!'; }
        else if (data.kelas)           { cls = 'warning'; icon = '!'; label = 'Sudah Diproses'; }
        else                           { cls = 'error';   icon = '✕'; label = 'Tidak Ditemukan'; }

        document.getElementById('sc-res-icon').textContent  = icon;
        document.getElementById('sc-res-label').textContent = label;
        document.getElementById('sc-res-sub').textContent   = data.message;

        const body = document.getElementById('sc-res-body');
        if (data.kelas) {
            const k = data.kelas;
            body.style.display = 'grid';
            body.innerHTML = `
                <div><div class="sc-info-key">Kelas</div><div class="sc-info-val">${k.nama_kelas}</div></div>
                <div><div class="sc-info-key">Status</div><div class="sc-info-val">${k.status}</div></div>
                ${k.waktu_ambil ? `<div><div class="sc-info-key">Waktu Ambil</div><div class="sc-info-val">${k.waktu_ambil}</div></div>` : ''}
                ${k.waktu_kembali ? `<div><div class="sc-info-key">Waktu Kembali</div><div class="sc-info-val">${k.waktu_kembali}</div></div>` : ''}
            `;
        } else {
            body.style.display = 'none';
            body.innerHTML = '';
        }

        scResult.className = cls;
        void scResult.offsetWidth;
        scResult.classList.add('show');
    }

    async function submitKelas(namaKelas) {
        if (isSending || !namaKelas) return;
        isSending = true; manualBtn.disabled = true;
        setStatus('Memproses ' + namaKelas + '…', 'loading');
        try {
            const res  = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ nama_kelas: namaKelas }),
            });
            const data = await res.json();
            showResult(data);
            setStatus(data.success ? '✓ Berhasil!' : '⚠ ' + data.message);
        } catch (_) {
            showResult({ success: false, message: 'Gagal terhubung ke server.', kelas: null });
            setStatus('Koneksi gagal. Coba lagi.');
        } finally {
            isSending = false; manualBtn.disabled = false;
            manualInput.value = ''; trap.value = '';
            refocusTrap();
        }
    }

    trap.addEventListener('input', () => {
        if (!trap.value) return;
        setScanReady('buffering'); clearTimeout(scanTimer);
        scanTimer = setTimeout(() => {
            const raw = trap.value.trim(); trap.value = '';
            if (raw.length >= MIN_LENGTH) submitKelas(raw); else setScanReady('ready');
        }, SCAN_TIMEOUT);
    });
    trap.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault(); clearTimeout(scanTimer);
        const raw = trap.value.trim(); trap.value = '';
        if (raw.length >= MIN_LENGTH) submitKelas(raw);
    });

    manualBtn.addEventListener('click', () => {
        const val = manualInput.value.trim();
        if (!val) { manualInput.focus(); return; }
        submitKelas(val);
    });
    manualInput.addEventListener('keydown', e => { if (e.key === 'Enter') manualBtn.click(); });

    let html5QrCode = null;
    function onScanSuccess(decodedText) { if (decodedText) submitKelas(decodedText.trim()); }
    function startFallbackScanner() {
        try {
            const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: { width: 220, height: 220 } }, false);
            scanner.render(onScanSuccess, () => {});
        } catch (_) { setStatus('Kamera tidak tersedia.'); }
    }
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            html5QrCode = new Html5Qrcode('reader');
            html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                onScanSuccess, () => {}
            ).then(() => setStatus('Arahkan kamera ke QR Kelas.'))
             .catch(() => startFallbackScanner());
        } else { startFallbackScanner(); }
    }).catch(() => startFallbackScanner());
</script>
</x-filament-panels::page>