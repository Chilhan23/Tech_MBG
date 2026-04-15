<x-filament-panels::page>
<style>
    .scanner-wrap {
        --c-bg:         #f8f7f5;
        --c-surface:    #ffffff;
        --c-surface-2:  #f2f1ef;
        --c-border:     #e8e6e2;
        --c-border-2:   #d4d0ca;
        --c-text:       #1a1916;
        --c-text-2:     #6b6860;
        --c-text-3:     #9b9890;
        --c-accent:     #dc2626;
        --c-accent-2:   #b91c1c;
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
        --shadow-lg:    0 12px 32px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06);
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
        display: flex; align-items: flex-start;
        justify-content: space-between;
        padding: 1.5rem 1.75rem;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        gap: 1rem;
    }
    .sc-page-title { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.03em; color: var(--c-text); line-height: 1.15; margin-bottom: 4px; }
    .sc-page-subtitle { font-size: 0.875rem; color: var(--c-text-2); font-weight: 500; }
    .sc-page-date { font-family: var(--font-mono); font-size: 0.78rem; color: var(--c-text-3); background: var(--c-surface-2); border: 1px solid var(--c-border); padding: 6px 12px; border-radius: 100px; white-space: nowrap; flex-shrink: 0; }

    /* ── Ready Bar ── */
    .sc-ready-bar { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: var(--radius); border: 1.5px solid; font-size: 0.875rem; font-weight: 600; transition: all 0.25s; }
    .sc-ready-bar.ready     { background: var(--c-green-bg); border-color: var(--c-green-bdr); color: #166534; }
    .sc-ready-bar.buffering { background: var(--c-blue-bg);  border-color: var(--c-blue-bdr);  color: #1e40af; animation: sc-blink .5s ease-in-out infinite; }
    .sc-ready-bar.lost      { background: var(--c-amber-bg); border-color: var(--c-amber-bdr); color: #854d0e; }
    .dark .sc-ready-bar.ready     { color: #86efac; }
    .dark .sc-ready-bar.buffering { color: #93c5fd; }
    .dark .sc-ready-bar.lost      { color: #fde68a; }
    @keyframes sc-blink { 0%,100%{opacity:1} 50%{opacity:.5} }
    .sc-pulse-dot { width: 10px; height: 10px; border-radius: 50%; background: currentColor; animation: sc-pulse 1.4s ease-in-out infinite; flex-shrink: 0; }
    @keyframes sc-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.35;transform:scale(.8)} }

    /* ── Grid ── */
    .sc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start; }
    @media (max-width: 860px) { .sc-grid { grid-template-columns: 1fr; } }

    /* ── Card ── */
    .sc-card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm); }
    .sc-card-header { display: flex; align-items: center; gap: 10px; padding: 14px 20px; border-bottom: 1px solid var(--c-border); background: var(--c-surface-2); }
    .sc-card-header-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--c-text-2); }
    .sc-card-body { padding: 18px 20px; }

    /* ── Camera ── */
    .sc-camera-wrap { width: 100%; aspect-ratio: 4/3; border-radius: var(--radius-sm); overflow: hidden; background: #0a0a0a; position: relative; }
    .sc-camera-overlay { position: absolute; inset: 0; pointer-events: none; z-index: 2; }
    .sc-camera-corner { position: absolute; width: 20px; height: 20px; border-color: rgba(255,255,255,.5); border-style: solid; }
    .sc-camera-corner.tl { top: 12px; left: 12px; border-width: 2px 0 0 2px; }
    .sc-camera-corner.tr { top: 12px; right: 12px; border-width: 2px 2px 0 0; }
    .sc-camera-corner.bl { bottom: 12px; left: 12px; border-width: 0 0 2px 2px; }
    .sc-camera-corner.br { bottom: 12px; right: 12px; border-width: 0 2px 2px 0; }
    #reader { width: 100%; height: 100%; }

    /* ── Status Bar ── */
    .sc-status { display: flex; align-items: center; gap: 8px; margin-top: 12px; padding: 10px 14px; border-radius: var(--radius-sm); background: var(--c-surface-2); border: 1px solid var(--c-border); font-size: 0.8125rem; font-weight: 500; color: var(--c-text-2); transition: all 0.2s; }
    .sc-status.loading { background: var(--c-blue-bg); border-color: var(--c-blue-bdr); color: #1d4ed8; }
    .dark .sc-status.loading { color: #93c5fd; }
    .sc-status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: sc-pulse 1.2s ease-in-out infinite; flex-shrink: 0; }

    /* ── Result Card ── */
    #sc-result { display: none; border-radius: var(--radius-sm); overflow: hidden; border: 1.5px solid transparent; }
    #sc-result.show { display: block; animation: sc-slide 0.22s cubic-bezier(.22,.68,0,1.2); }
    @keyframes sc-slide { from { opacity: 0; transform: translateY(10px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .sc-result-head { display: flex; align-items: center; gap: 14px; padding: 14px 16px; }
    .sc-result-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; font-weight: 900; flex-shrink: 0; }
    .sc-result-label { font-weight: 700; font-size: 0.9375rem; }
    .sc-result-sub   { font-size: 0.8rem; margin-top: 2px; opacity: 0.7; }

    /* ── Result Body (info grid) ── */
    .sc-result-body { padding: 12px 16px 14px; }
    .sc-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px; }
    .sc-info-key { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.5; margin-bottom: 2px; }
    .sc-info-val { font-size: 0.875rem; font-weight: 700; font-family: var(--font-mono); }

    /* ── Perbandingan bar (ompreng vs scan) ── */
    .sc-compare { margin-top: 12px; padding-top: 12px; border-top: 1px solid currentColor; opacity: 0.15; }
    .sc-compare { opacity: 1; border-color: rgba(0,0,0,.08); }
    .dark .sc-compare { border-color: rgba(255,255,255,.08); }
    .sc-compare-title { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.5; margin-bottom: 8px; }
    .sc-compare-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 0.8rem; font-weight: 600; }
    .sc-compare-bar-wrap { flex: 1; height: 6px; border-radius: 99px; background: rgba(0,0,0,.08); overflow: hidden; }
    .dark .sc-compare-bar-wrap { background: rgba(255,255,255,.08); }
    .sc-compare-bar { height: 100%; border-radius: 99px; transition: width 0.6s cubic-bezier(.22,.68,0,1.2); }
    .sc-compare-num { font-family: var(--font-mono); font-size: 0.78rem; font-weight: 700; min-width: 28px; text-align: right; opacity: 0.75; }

    /* ── Sisa badge ── */
    .sc-sisa-badge { display: inline-flex; align-items: center; gap: 5px; margin-top: 10px; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; border: 1.5px solid; }

    /* success */
    #sc-result.success { border-color: var(--c-green-bdr); }
    #sc-result.success .sc-result-head { background: var(--c-green-bg); color: #166534; }
    #sc-result.success .sc-result-icon { background: #bbf7d0; color: #15803d; }
    #sc-result.success .sc-result-body { background: #f7fef9; color: #14532d; }
    #sc-result.success .sc-compare-bar.ompreng { background: #16a34a; }
    #sc-result.success .sc-compare-bar.scan    { background: #4ade80; }
    #sc-result.success .sc-sisa-badge { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .dark #sc-result.success .sc-result-head { color: #86efac; }
    .dark #sc-result.success .sc-result-body { background: #031a0e; color: #bbf7d0; }

    /* warning */
    #sc-result.warning { border-color: var(--c-amber-bdr); }
    #sc-result.warning .sc-result-head { background: var(--c-amber-bg); color: #92400e; }
    #sc-result.warning .sc-result-icon { background: #fde68a; color: #b45309; }
    #sc-result.warning .sc-result-body { background: #fffef7; color: #78350f; }
    #sc-result.warning .sc-compare-bar.ompreng { background: #f59e0b; }
    #sc-result.warning .sc-compare-bar.scan    { background: #fcd34d; }
    #sc-result.warning .sc-sisa-badge { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .dark #sc-result.warning .sc-result-head { color: #fde68a; }
    .dark #sc-result.warning .sc-result-body { background: #1c1000; color: #fef3c7; }

    /* error */
    #sc-result.error { border-color: var(--c-red-bdr); }
    #sc-result.error .sc-result-head { background: var(--c-red-bg); color: #991b1b; }
    #sc-result.error .sc-result-icon { background: #fecaca; color: #b91c1c; }
    #sc-result.error .sc-result-body { background: #fff8f8; color: #7f1d1d; }
    #sc-result.error .sc-compare-bar.ompreng { background: #ef4444; }
    #sc-result.error .sc-compare-bar.scan    { background: #fca5a5; }
    #sc-result.error .sc-sisa-badge { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .dark #sc-result.error .sc-result-head { color: #fca5a5; }
    .dark #sc-result.error .sc-result-body { background: #1c0505; color: #fecaca; }

    /* ── Placeholder ── */
    .sc-placeholder { padding: 32px 0; text-align: center; color: var(--c-text-3); font-size: 0.875rem; }

    /* ── Manual Input ── */
    .sc-manual-row { display: flex; gap: 8px; }
    .sc-manual-input { flex: 1; padding: 11px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--c-border); font-size: 0.9375rem; font-family: var(--font-mono); font-weight: 500; outline: none; transition: border-color 0.15s, box-shadow 0.15s; background: var(--c-bg); color: var(--c-text); }
    .sc-manual-input::placeholder { color: var(--c-text-3); font-family: var(--font); font-weight: 400; }
    .sc-manual-input:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(220,38,38,.12); }
    .sc-manual-btn { padding: 11px 22px; border-radius: var(--radius-sm); border: none; background: var(--c-accent); color: white; font-size: 0.875rem; font-weight: 700; font-family: var(--font); cursor: pointer; transition: background 0.15s, transform 0.1s, box-shadow 0.15s; box-shadow: 0 2px 8px rgba(220,38,38,.3); white-space: nowrap; }
    .sc-manual-btn:hover   { background: var(--c-accent-2); box-shadow: 0 4px 12px rgba(220,38,38,.4); }
    .sc-manual-btn:active  { transform: scale(0.97); }
    .sc-manual-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }

    .sc-right-col { display: flex; flex-direction: column; gap: 1.25rem; }

    /* ── html5-qrcode overrides ── */
    #reader { border: none !important; padding: 0 !important; }
    #reader__scan_region { border-radius: var(--radius-sm) !important; overflow: hidden !important; }
    #reader__scan_region video { border-radius: var(--radius-sm) !important; object-fit: cover !important; width: 100% !important; height: 100% !important; }
    #reader__scan_region img { display: none !important; }
    #reader__header_message, #reader__status_span { display: none !important; }
    #reader__dashboard_section_csr button { background: var(--c-accent) !important; color: white !important; border: none !important; border-radius: var(--radius-sm) !important; padding: 10px 20px !important; font-weight: 700 !important; font-family: var(--font) !important; cursor: pointer !important; width: 100% !important; margin-top: 8px !important; }
    #reader__dashboard_section_csr select { border-radius: var(--radius-sm) !important; border: 1.5px solid var(--c-border) !important; padding: 8px 12px !important; background: var(--c-bg) !important; color: var(--c-text) !important; font-family: var(--font) !important; width: 100% !important; }
    #reader__dashboard_section_fsr span, #reader__filescan_input { display: none !important; }

    /* ── Modal ── */
    /* ── MODAL BODY - DARK ── */
    .sc-modal {
        background: #0f172a !important;
        border-color: #1e3a8a !important;
    }
    .sc-modal-body {
        background: #0f172a !important;
    }
    .sc-modal-info {
        background: #1e293b !important;
        border-color: #1e3a8a !important;
        color: #93c5fd !important;
    }
    .sc-modal-label {
        color: #60a5fa !important;
    }
    .sc-modal-footer {
        background: #0f172a !important;
        border-top-color: #1e3a8a !important;
    }
    .sc-modal-cancel {
        background: #1e293b !important;
        border-color: #1e3a8a !important;
        color: #93c5fd !important;
    }
    .sc-modal-cancel:hover {
        background: #1e3a8a !important;
        color: white !important;
    }
    .sc-modal-chip {
        background: #1e293b !important;
        border-color: #1e3a8a !important;
        color: #93c5fd !important;
    }
    .sc-modal-chip:hover {
        background: #1e3a8a !important;
        color: white !important;
        border-color: #3b82f6 !important;
    }
    .sc-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; opacity: 0; pointer-events: none; transition: opacity 0.2s ease; }
    .sc-modal-backdrop.open { opacity: 1; pointer-events: all; }
    .sc-modal { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 18px; box-shadow: var(--shadow-lg); width: 100%; max-width: 420px; overflow: hidden; transform: translateY(20px) scale(0.97); transition: transform 0.25s cubic-bezier(.22,.68,0,1.2); }
    .sc-modal-backdrop.open .sc-modal { transform: translateY(0) scale(1); }
    .sc-modal-head { background: var(--c-red-bg); border-bottom: 1px solid var(--c-red-bdr); padding: 1.25rem 1.5rem 1rem; }
    .dark .sc-modal-head { background: #2d0a0a; border-color: #7f1d1d; }
    .sc-modal-kelas-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--c-accent); color: white; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 4px 10px; border-radius: 100px; margin-bottom: 10px; }
    .sc-modal-title { font-size: 1.15rem; font-weight: 800; color: var(--c-text); letter-spacing: -0.02em; margin-bottom: 4px; color: #93c5fd !important; }
    .sc-modal-sub { font-size: 0.8125rem; color: var(--c-text-2); font-weight: 500; color: #93c5fd !important; }
    .sc-modal-body { padding: 1.25rem 1.5rem; }
    .sc-modal-info { display: flex; align-items: center; gap: 10px; background: var(--c-surface-2); border: 1px solid var(--c-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-bottom: 1.1rem; font-size: 0.8125rem; color: var(--c-text-2); font-weight: 500; }
    .sc-modal-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--c-text-3); margin-bottom: 8px; display: block; }
    .sc-modal-input-wrap { display: flex; align-items: center; border: 2px solid var(--c-border); border-radius: var(--radius-sm); overflow: hidden; transition: border-color 0.15s, box-shadow 0.15s; background: var(--c-bg); }
    /* ── INPUT JUMLAH OMPRENG - DARK BG ── */
    .sc-modal-input-wrap {
        background: #0f172a !important;
        border-color: #1e3a8a !important;
    }
    .sc-modal-input-wrap:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,.2) !important;
        background: #0f172a !important;
    }
    .sc-modal-stepper {
        background: #1e293b !important;
        color: #93c5fd !important;
    }
    .sc-modal-stepper:hover {
        background: #1e3a8a !important;
    }
    .sc-modal-stepper.minus {
        border-right-color: #1e3a8a !important;
    }
    .sc-modal-stepper.plus {
        border-left-color: #1e3a8a !important;
    }
    #modal-jumlah {
        color: #e0f2fe !important;
        background: transparent !important;
    }
    .sc-modal-input-wrap:focus-within { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(220,38,38,.12); }
    .sc-modal-stepper { width: 42px; height: 52px; border: none; background: var(--c-surface-2); color: var(--c-text); font-size: 1.25rem; font-weight: 700; cursor: pointer; transition: background 0.12s; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none; }
    .sc-modal-stepper:hover { background: var(--c-border); }
    .sc-modal-stepper.minus { border-right: 1px solid var(--c-border); }
    .sc-modal-stepper.plus  { border-left:  1px solid var(--c-border); }
    #modal-jumlah { flex: 1; border: none; outline: none; background: transparent; text-align: center; font-size: 1.75rem; font-weight: 800; font-family: var(--font-mono); color: var(--c-text); padding: 0; height: 52px; }
    .sc-modal-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
    .sc-modal-chip { padding: 4px 12px; border-radius: 100px; border: 1.5px solid var(--c-border); font-size: 0.8rem; font-weight: 700; font-family: var(--font-mono); color: var(--c-text-2); cursor: pointer; background: var(--c-surface); transition: all 0.12s; }
    .sc-modal-chip:hover { border-color: var(--c-accent); color: var(--c-accent); background: var(--c-red-bg); }
    .sc-modal-chip.active { border-color: var(--c-accent); background: var(--c-accent); color: white; }
    .sc-modal-footer { display: flex; gap: 8px; padding: 1rem 1.5rem 1.25rem; border-top: 1px solid var(--c-border); }
    .sc-modal-cancel { flex: 1; padding: 11px; border-radius: var(--radius-sm); border: 1.5px solid var(--c-border); background: var(--c-surface); color: var(--c-text-2); font-size: 0.875rem; font-weight: 700; font-family: var(--font); cursor: pointer; transition: all 0.12s; }
    .sc-modal-cancel:hover { border-color: var(--c-border-2); color: var(--c-text); background: var(--c-surface-2); }
    .sc-modal-confirm { flex: 2; padding: 11px; border-radius: var(--radius-sm); border: none; background: var(--c-accent); color: white; font-size: 0.875rem; font-weight: 800; font-family: var(--font); cursor: pointer; transition: background 0.12s, box-shadow 0.12s, transform 0.1s; box-shadow: 0 2px 8px rgba(220,38,38,.3); }
    .sc-modal-confirm:hover   { background: var(--c-accent-2); box-shadow: 0 4px 12px rgba(220,38,38,.4); }
    .sc-modal-confirm:active  { transform: scale(0.98); }
    .sc-modal-confirm:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }
</style>

{{-- Barcode trap --}}
<input id="barcode-trap" type="text" autocomplete="off" autocorrect="off" spellcheck="false" tabindex="-1" aria-hidden="true"
       style="position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">

{{-- ═══ MODAL JUMLAH OMPRENG ═══ --}}
<div id="modal-backdrop" class="sc-modal-backdrop" role="dialog" aria-modal="true">
    <div class="sc-modal">
        <div class="sc-modal-head">
            <div class="sc-modal-kelas-badge">📦 <span id="modal-kelas-badge">—</span></div>
            <div class="sc-modal-title">Jumlah Ompreng Diambil</div>
            <div class="sc-modal-sub">Masukkan jumlah ompreng yang diambil dari pusat</div>
        </div>
        <div class="sc-modal-body">
            <div class="sc-modal-info">
                <span>👥</span>
                <span>Jumlah siswa kelas ini: <strong id="modal-jumlah-siswa" style="font-family:var(--font-mono)">—</strong> orang</span>
            </div>
            <label class="sc-modal-label" for="modal-jumlah">Jumlah Ompreng</label>
            <div class="sc-modal-input-wrap">
                <button type="button" class="sc-modal-stepper minus" id="btn-minus">−</button>
                <input id="modal-jumlah" type="number" min="1" max="999" value="30" inputmode="numeric">
                <button type="button" class="sc-modal-stepper plus" id="btn-plus">+</button>
            </div>
            <div class="sc-modal-chips" id="modal-chips"></div>
        </div>
        <div class="sc-modal-footer">
            <button type="button" class="sc-modal-cancel" id="modal-cancel">Batal</button>
            <button type="button" class="sc-modal-confirm" id="modal-confirm">✓ Catat Pengambilan</button>
        </div>
    </div>
</div>

{{-- ═══ HALAMAN UTAMA ═══ --}}
<div class="scanner-wrap">
    <div class="sc-page-header">
        <div>
            <div class="sc-page-title">Scanner Kelas — Ambil Ompreng</div>
            <div class="sc-page-subtitle">Scan QR Kelas untuk mencatat pengambilan ompreng dari pusat</div>
        </div>
        <div class="sc-page-date" id="sc-live-date">—</div>
    </div>

    <div class="sc-ready-bar ready" id="scan-ready-bar">
        <span class="sc-pulse-dot"></span>
        <span id="scan-ready-text">Siap scan — arahkan alat barcode ke QR Kelas</span>
    </div>

    <div class="sc-grid">

        {{-- Kiri: Kamera --}}
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

        {{-- Kanan: Hasil + Manual --}}
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
                                <div class="sc-result-sub"   id="sc-res-sub"></div>
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
        document.getElementById('sc-live-date').textContent =
            new Date().toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    }
    updateDate();
    setInterval(updateDate, 60000);
</script>

<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
(function () {
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

    const backdrop      = document.getElementById('modal-backdrop');
    const modalKelas    = document.getElementById('modal-kelas-badge');
    const modalSiswa    = document.getElementById('modal-jumlah-siswa');
    const modalJumlah   = document.getElementById('modal-jumlah');
    const modalChips    = document.getElementById('modal-chips');
    const btnMinus      = document.getElementById('btn-minus');
    const btnPlus       = document.getElementById('btn-plus');
    const btnCancel     = document.getElementById('modal-cancel');
    const btnConfirm    = document.getElementById('modal-confirm');

    let scanTimer    = null;
    let isSending    = false;
    let pendingKelas = null;

    /* ── Focus trap ── */
    const isModalEl = () => backdrop.classList.contains('open');
    function refocusTrap() {
        if (isModalEl()) return;
        if (document.activeElement === manualInput) return;
        trap.focus();
    }
    setInterval(() => { if (!isModalEl() && document.activeElement !== manualInput && document.activeElement !== trap) trap.focus(); }, 800);
    window.addEventListener('load', () => setTimeout(refocusTrap, 200));
    document.addEventListener('click', (e) => { if (e.target !== manualInput && !backdrop.contains(e.target)) setTimeout(refocusTrap, 50); });
    manualInput.addEventListener('blur', () => setTimeout(refocusTrap, 150));
    trap.addEventListener('focus', () => setScanReady('ready'));
    trap.addEventListener('blur', () => { if (document.activeElement !== manualInput && !isModalEl()) setScanReady('lost'); });

    /* ── Helpers ── */
    function setStatus(text, type = 'idle') {
        statusText.textContent = text;
        statusBar.className = 'sc-status' + (type === 'loading' ? ' loading' : '');
    }
    function setScanReady(state) {
        scanReadyBar.className = 'sc-ready-bar ' + state;
        scanReadyTxt.textContent = {
            ready:     'Siap scan — arahkan alat barcode ke QR Kelas',
            buffering: 'Menerima data scanner…',
            lost:      'Klik area ini agar alat scan bisa terbaca',
        }[state] ?? '';
    }

    /* ── Render result ── */
    function showResult(data) {
        scPlaceholder.style.display = 'none';
        scResult.classList.remove('show', 'success', 'warning', 'error');

        let cls, icon, label;
        if (data.success)    { cls = 'success'; icon = '✓'; label = 'Berhasil!'; }
        else if (data.kelas) { cls = 'warning'; icon = '!'; label = 'Perhatian'; }
        else                 { cls = 'error';   icon = '✕'; label = 'Gagal'; }

        document.getElementById('sc-res-icon').textContent  = icon;
        document.getElementById('sc-res-label').textContent = label;
        document.getElementById('sc-res-sub').textContent   = data.message;

        const body = document.getElementById('sc-res-body');
        if (data.kelas) {
            const k = data.kelas;
            body.style.display = 'block';

            // Hitung max untuk bar
            const ompreng  = k.ompreng_diambil    ?? null;
            const scanAmbil  = k.siswa_scan_ambil  ?? null;
            const scanKembali= k.siswa_scan_kembali ?? null;
            const sisa       = k.sisa_ompreng      ?? null;
            const maxVal     = ompreng !== null ? Math.max(ompreng, scanAmbil ?? 0, 1) : 1;

            // Info grid
            let infoHtml = '<div class="sc-info-grid">';
            if (k.nama_kelas)    infoHtml += row('Kelas', k.nama_kelas);
            if (k.status)        infoHtml += row('Status', k.status.replace(/_/g, ' '));
            if (k.waktu_ambil)   infoHtml += row('Waktu Ambil', k.waktu_ambil);
            if (k.waktu_kembali) infoHtml += row('Waktu Kembali', k.waktu_kembali);
            if (k.batas_kembali) infoHtml += row('Batas Kembali', k.batas_kembali);
            if (k.terlambat)     infoHtml += row('Terlambat', k.terlambat);
            if (k.belum_kembali != null) infoHtml += row('Belum Kembali', k.belum_kembali + ' siswa');
            infoHtml += '</div>';

            // Perbandingan bar — tampil kalau ada data ompreng
            let compareHtml = '';
            if (ompreng !== null && scanAmbil !== null) {
                const pctOmpreng   = Math.round(ompreng    / maxVal * 100);
                const pctScanAmbil = Math.round(scanAmbil  / maxVal * 100);
                const pctScanKbli  = scanKembali !== null ? Math.round(scanKembali / maxVal * 100) : null;

                compareHtml = `
                <div class="sc-compare">
                    <div class="sc-compare-title">Perbandingan ompreng vs scan</div>
                    <div class="sc-compare-row">
                        <span style="min-width:110px;font-size:.75rem;opacity:.7">📦 Ompreng diambil</span>
                        <div class="sc-compare-bar-wrap">
                            <div class="sc-compare-bar ompreng" style="width:${pctOmpreng}%"></div>
                        </div>
                        <span class="sc-compare-num">${ompreng}</span>
                    </div>
                    <div class="sc-compare-row">
                        <span style="min-width:110px;font-size:.75rem;opacity:.7">🙋 Siswa scan ambil</span>
                        <div class="sc-compare-bar-wrap">
                            <div class="sc-compare-bar scan" style="width:${pctScanAmbil}%"></div>
                        </div>
                        <span class="sc-compare-num">${scanAmbil}</span>
                    </div>
                    ${pctScanKbli !== null ? `
                    <div class="sc-compare-row">
                        <span style="min-width:110px;font-size:.75rem;opacity:.7">↩️ Siswa scan kembali</span>
                        <div class="sc-compare-bar-wrap">
                            <div class="sc-compare-bar scan" style="width:${pctScanKbli}%"></div>
                        </div>
                        <span class="sc-compare-num">${scanKembali}</span>
                    </div>` : ''}
                    ${sisa !== null ? `
                    <div>
                        <span class="sc-sisa-badge">
                            📤 Sisa ompreng tidak diambil: <strong>${sisa}</strong>
                        </span>
                    </div>` : ''}
                </div>`;
            }

            body.innerHTML = infoHtml + compareHtml;
        } else {
            body.style.display = 'none';
            body.innerHTML = '';
        }

        scResult.className = cls;
        void scResult.offsetWidth;
        scResult.classList.add('show');
    }

    function row(key, val) {
        return `<div><div class="sc-info-key">${key}</div><div class="sc-info-val">${val}</div></div>`;
    }

    /* ── Modal ── */
    function openModal(namaKelas, jumlahSiswa) {
        pendingKelas = namaKelas;
        modalKelas.textContent  = namaKelas;
        modalSiswa.textContent  = jumlahSiswa ?? '—';
        const def = jumlahSiswa ?? 30;
        modalJumlah.value = def;
        buildChips(def);
        backdrop.classList.add('open');
        setTimeout(() => modalJumlah.focus(), 250);
    }

    function closeModal(resetFields = true) {
        backdrop.classList.remove('open');
        pendingKelas = null;
        if (resetFields) { manualInput.value = ''; trap.value = ''; }
        setTimeout(refocusTrap, 300);
    }

    function buildChips(base) {
        const picks = [...new Set([base - 5, base - 2, base, base + 2, base + 5].filter(v => v > 0))];
        modalChips.innerHTML = picks.map(v =>
            `<button type="button" class="sc-modal-chip${v === base ? ' active' : ''}" data-val="${v}">${v}</button>`
        ).join('');
        modalChips.querySelectorAll('.sc-modal-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                modalJumlah.value = chip.dataset.val;
                modalChips.querySelectorAll('.sc-modal-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
            });
        });
    }

    modalJumlah.addEventListener('input', () => {
        const v = parseInt(modalJumlah.value);
        modalChips.querySelectorAll('.sc-modal-chip').forEach(c => c.classList.toggle('active', parseInt(c.dataset.val) === v));
    });
    btnMinus.addEventListener('click', () => { modalJumlah.value = Math.max(1, parseInt(modalJumlah.value || 1) - 1); modalJumlah.dispatchEvent(new Event('input')); });
    btnPlus.addEventListener('click',  () => { modalJumlah.value = Math.min(999, parseInt(modalJumlah.value || 0) + 1); modalJumlah.dispatchEvent(new Event('input')); });
    backdrop.addEventListener('click', e => { if (e.target === backdrop) closeModal(); });
    btnCancel.addEventListener('click', () => { closeModal(); setStatus('Dibatalkan.'); isSending = false; manualBtn.disabled = false; });
    modalJumlah.addEventListener('keydown', e => { if (e.key === 'Enter') btnConfirm.click(); });

    btnConfirm.addEventListener('click', async () => {
        const nama   = pendingKelas;
        const jumlah = parseInt(modalJumlah.value);
        if (!nama) return;
        if (!jumlah || jumlah < 1) {
            modalJumlah.style.outline = '2px solid #dc2626';
            setTimeout(() => modalJumlah.style.outline = '', 1200);
            return;
        }
        closeModal(false);
        await submitKelas(nama, jumlah);
    });

    /* ── Submit ke API ── */
    async function submitKelas(namaKelas, jumlahOmpreng = null) {
        setStatus('Memproses ' + namaKelas + '…', 'loading');
        const body = { nama_kelas: namaKelas };
        if (jumlahOmpreng !== null) body.jumlah_ompreng = jumlahOmpreng;

        try {
            const res  = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            showResult(data);
            setStatus(data.success ? '✓ Berhasil!' : '⚠ ' + data.message);
        } catch (_) {
            showResult({ success: false, message: 'Gagal terhubung ke server.', kelas: null });
            setStatus('Koneksi gagal. Coba lagi.');
        } finally {
            isSending = false;
            manualBtn.disabled = false;
            manualInput.value  = '';
            trap.value         = '';
            refocusTrap();
        }
    }

    /* ── Handle scan: cek apakah belum diambil → modal, sudah diambil → langsung kirim ── */
    async function handleScan(namaKelas) {
        if (isSending || !namaKelas) return;
        isSending = true;
        manualBtn.disabled = true;
        setStatus('Mengecek kelas ' + namaKelas + '…', 'loading');

        try {
            // Kirim POST dulu dengan jumlah_ompreng = null untuk tahu statusnya
            // Backend: kalau belum ada log → BUTUH jumlah ompreng → kita intercept dari response
            // Tapi karena apiKelasStore langsung create kalau belum ada log,
            // kita pakai apiKelasCheck dulu (GET) untuk cek status tanpa mengubah data
            const checkRes  = await fetch('{{ route('scanner.api.kelas.check') }}?nama_kelas=' + encodeURIComponent(namaKelas), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const checkData = await checkRes.json();

            if (!checkData.found) {
                showResult({ success: false, message: checkData.message ?? 'Kelas tidak ditemukan.', kelas: null });
                setStatus('⚠ Kelas tidak ditemukan.');
                isSending = false; manualBtn.disabled = false;
                manualInput.value = ''; trap.value = '';
                refocusTrap();
                return;
            }

            if (checkData.status === 'belum_diambil') {
                // Tampilkan modal — isSending tetap true sampai modal confirm/cancel
                setStatus('Masukkan jumlah ompreng…');
                openModal(namaKelas, checkData.jumlah_siswa ?? null);
                return; // jangan reset isSending
            }

            // Sudah diambil atau selesai → langsung submit tanpa modal
            await submitKelas(namaKelas, null);

        } catch (_) {
            showResult({ success: false, message: 'Gagal terhubung ke server.', kelas: null });
            setStatus('Koneksi gagal. Coba lagi.');
            isSending = false; manualBtn.disabled = false;
            manualInput.value = ''; trap.value = '';
            refocusTrap();
        }
    }

    /* ── Barcode gun ── */
    trap.addEventListener('input', () => {
        if (!trap.value) return;
        setScanReady('buffering'); clearTimeout(scanTimer);
        scanTimer = setTimeout(() => {
            const raw = trap.value.trim(); trap.value = '';
            if (raw.length >= MIN_LENGTH) handleScan(raw); else setScanReady('ready');
        }, SCAN_TIMEOUT);
    });
    trap.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault(); clearTimeout(scanTimer);
        const raw = trap.value.trim(); trap.value = '';
        if (raw.length >= MIN_LENGTH) handleScan(raw);
    });

    /* ── Manual input ── */
    manualBtn.addEventListener('click', () => {
        const val = manualInput.value.trim();
        if (!val) { manualInput.focus(); return; }
        handleScan(val);
    });
    manualInput.addEventListener('keydown', e => { if (e.key === 'Enter') manualBtn.click(); });

    /* ── Kamera QR ── */
    function onScanSuccess(decodedText) { if (decodedText) handleScan(decodedText.trim()); }
    function startFallbackScanner() {
        try { new Html5QrcodeScanner('reader', { fps: 10, qrbox: { width: 800, height: 400 } }, false).render(onScanSuccess, () => {}); }
        catch (_) { setStatus('Kamera tidak tersedia.'); }
    }
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            const cam = new Html5Qrcode('reader');
            cam.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 400, height: 400 } }, onScanSuccess, () => {})
               .then(() => setStatus('Arahkan kamera ke QR Kelas.'))
               .catch(() => startFallbackScanner());
        } else { startFallbackScanner(); }
    }).catch(() => startFallbackScanner());

})();
</script>
</x-filament-panels::page>