<x-filament-panels::page>

{{-- ========================================================
     STYLES & KEYFRAMES
     ======================================================== --}}
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleInX {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }
    @keyframes pulseDot {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.35; }
    }

    .db-anim-1 { animation: fadeUp .45s ease both; }
    .db-anim-2 { animation: fadeUp .45s .07s ease both; }
    .db-anim-3 { animation: fadeUp .45s .14s ease both; }
    .db-anim-4 { animation: fadeUp .45s .21s ease both; }
    .db-anim-5 { animation: fadeUp .45s .28s ease both; }
    .db-anim-6 { animation: fadeUp .45s .35s ease both; }

    .db-stat-card {
        transition: transform .2s ease;
    }
    .db-stat-card:hover {
        transform: translateY(-3px);
    }

    .db-progress-fill {
        transform-origin: left;
        animation: scaleInX .8s cubic-bezier(0.16,1,0.3,1) both;
    }

    .db-pulse {
        animation: pulseDot 2s infinite;
    }
</style>

{{-- ========================================================
     ACCOUNT WIDGET
     ======================================================== --}}
@livewire(\Filament\Widgets\AccountWidget::class)

{{-- ========================================================
     GREETING HEADER
     ======================================================== --}}
<div class="flex items-center justify-between mt-2 mb-5 db-anim-1">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ $greeting }}, {{ $userName }}!
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-500 mt-1">
            {{ $stats['tanggal'] }}
        </p>
    </div>

    @if($isSuperAdmin)
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                     bg-purple-50 text-purple-800 border border-purple-200
                     dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 db-pulse"></span>
            Super Admin
        </span>
    @else
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                     bg-green-50 text-green-800 border border-green-200
                     dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 db-pulse"></span>
            Admin Kelas
        </span>
    @endif
</div>

{{-- ========================================================
     INFO BANNER
     ======================================================== --}}
<div class="db-anim-2">
    @if($isSuperAdmin)
        <div class="flex items-start gap-3 p-4 mb-5 rounded-xl bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400">
            <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"/>
            <p class="text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                Kamu login sebagai <strong>Super Admin</strong> — menampilkan data dari
                <strong>semua kelas</strong>. Rekap lengkap seluruh sekolah bisa kamu pantau di sini.
            </p>
        </div>
    @else
        <div class="flex items-start gap-3 p-4 mb-5 rounded-xl bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400">
            <x-heroicon-o-academic-cap class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0"/>
            <p class="text-sm text-green-700 dark:text-green-300 leading-relaxed">
                Kamu login sebagai <strong>Admin {{ $stats['scope'] }}</strong>
            </p>
        </div>
    @endif
</div>

{{-- ========================================================
     STATS CARDS
     ======================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

    {{-- Total Siswa --}}
    <div class="db-stat-card relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 p-5 overflow-hidden db-anim-3">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-l-2xl"></div>
        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center mb-3">
            <x-heroicon-o-users class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
        </div>
        <p class="text-3xl font-semibold text-gray-900 dark:text-white"
           data-countup="{{ $stats['total_siswa'] }}">
            {{ number_format($stats['total_siswa']) }}
        </p>
        <p class="text-sm text-gray-600 mt-1">Total siswa</p>
        <span class="inline-block mt-2 px-2.5 py-1 rounded-full text-xs font-medium
                     bg-blue-50 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
            {{ $stats['scope'] }}
        </span>
    </div>

    {{-- Sudah Ambil --}}
    <div class="db-stat-card relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 p-5 overflow-hidden db-anim-4">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 rounded-l-2xl"></div>
        <div class="w-9 h-9 rounded-xl bg-green-50 dark:bg-green-900/40 flex items-center justify-center mb-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400"/>
        </div>
        <p class="text-3xl font-semibold text-gray-900 dark:text-white"
           data-countup="{{ $stats['sudah_ambil'] }}">
            {{ number_format($stats['sudah_ambil']) }}
        </p>
        <p class="text-sm text-gray-600 mt-1">Sudah ambil MBG</p>
        <span class="inline-block mt-2 px-2.5 py-1 rounded-full text-xs font-medium
                     bg-green-50 text-green-800 dark:bg-green-900/40 dark:text-green-300">
            {{ $stats['persen'] }}% dari total
        </span>
    </div>

    {{-- Belum Ambil --}}
    <div class="db-stat-card relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 p-5 overflow-hidden db-anim-5">
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl
                    {{ $stats['belum_ambil'] > 0 ? 'bg-red-500' : 'bg-green-500' }}"></div>
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3
                    {{ $stats['belum_ambil'] > 0 ? 'bg-red-50 dark:bg-red-900/40' : 'bg-green-50 dark:bg-green-900/40' }}">
            @if($stats['belum_ambil'] > 0)
                <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 dark:text-red-400"/>
            @else
                <x-heroicon-o-check-badge class="w-5 h-5 text-green-600 dark:text-green-400"/>
            @endif
        </div>
        <p class="text-3xl font-semibold text-gray-900 dark:text-white"
           data-countup="{{ $stats['belum_ambil'] }}">
            {{ number_format($stats['belum_ambil']) }}
        </p>
        <p class="text-sm text-gray-600 mt-1">Belum ambil MBG</p>
        <span class="inline-block mt-2 px-2.5 py-1 rounded-full text-xs font-medium
                     {{ $stats['belum_ambil'] > 0
                         ? 'bg-red-50 text-red-800 dark:bg-red-900/40 dark:text-red-300'
                         : 'bg-green-50 text-green-800 dark:bg-green-900/40 dark:text-green-300' }}">
            {{ $stats['belum_ambil'] > 0
                ? $stats['belum_ambil'].' siswa belum scan'
                : 'Semua sudah ambil!' }}
        </span>
    </div>

</div>

{{-- ========================================================
     CHART TREN + PROGRESS KELAS
     ======================================================== --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-4 mb-4 db-anim-5">

    {{-- Line Chart --}}
    <div class="xl:col-span-3 bg-white dark:bg-gray-800 rounded-2xl border
                border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs uppercase tracking-wider font-medium text-gray-600">
                Tren absensi 7 hari terakhir
            </p>
            <span class="flex items-center gap-1.5 text-xs text-gray-600">
                <span class="w-2.5 h-2.5 rounded-sm bg-green-500 inline-block"></span>Sudah ambil
            </span>
        </div>
        <div style="position:relative;height:200px">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- Progress Per Kelas --}}
    <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border
                border-gray-100 dark:border-gray-700 p-5">
        <p class="text-xs uppercase tracking-wider font-medium text-gray-600 mb-4">
            Progress per kelas
        </p>
        <div class="flex flex-col gap-3">
            @foreach($kelasData as $i => $k)
                @php
                    $pct    = $k['total'] > 0 ? round($k['sudah'] / $k['total'] * 100) : 0;
                    $color  = $pct == 100 ? 'bg-green-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-red-400');
                    $txtCol = $pct == 100 ? 'text-green-600' : ($pct >= 80 ? 'text-amber-600' : 'text-red-500');
                    $delay  = 0.35 + ($i * 0.07);
                @endphp
                <div style="animation: fadeUp .4s {{ $delay }}s ease both; opacity:0; animation-fill-mode:both;">
                    <div class="flex justify-between items-center mb-1">
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $k['nama'] }}
                            </span>
                            <span class="text-xs text-gray-600 ml-1">
                                {{ $k['sudah'] }}/{{ $k['total'] }}
                            </span>
                        </div>
                        <span class="text-xs font-medium {{ $txtCol }}">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $color }} rounded-full db-progress-fill"
                             style="width:{{ $pct }}%; animation-delay:{{ $delay + 0.1 }}s;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ========================================================
     BAR CHART DISTRIBUSI PER KELAS (Super Admin only)
     ======================================================== --}}
@if($isSuperAdmin)
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
            dark:border-gray-700 p-5 db-anim-6">
    <div class="flex items-center gap-4 mb-3">
        <p class="text-xs uppercase tracking-wider font-medium text-gray-600">
            Distribusi absensi per kelas hari ini
        </p>
        <div class="flex gap-3 ml-auto">
            <span class="flex items-center gap-1.5 text-xs text-gray-600">
                <span class="w-2.5 h-2.5 rounded-sm bg-green-500 inline-block"></span>Sudah
            </span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600">
                <span class="w-2.5 h-2.5 rounded-sm bg-red-400 inline-block"></span>Belum
            </span>
        </div>
    </div>
    <div style="position:relative;height:190px">
        <canvas id="barChart"></canvas>
    </div>
</div>
@endif

{{-- ========================================================
     SCRIPTS
     ======================================================== --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Count-up animation ── */
    function animateCountUp(el, target, duration) {
        const start = performance.now();
        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            const ease     = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(ease * target).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    setTimeout(function () {
        document.querySelectorAll('[data-countup]').forEach(function (el) {
            const target = parseInt(el.dataset.countup, 10);
            animateCountUp(el, target, 900);
        });
    }, 300);

    /* ── Chart config ── */
    const isDark    = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.04)';
    const tickColor = isDark ? '#6b7280' : '#9ca3af';

    const tooltipDefaults = {
        backgroundColor : isDark ? '#1f2937' : '#ffffff',
        borderColor     : gridColor,
        borderWidth     : 1,
        titleColor      : isDark ? '#f9fafb' : '#111827',
        bodyColor       : isDark ? '#9ca3af' : '#6b7280',
        padding         : 10,
        cornerRadius    : 8,
    };

    const scaleDefaults = {
        x: {
            grid  : { color: gridColor },
            ticks : { color: tickColor, font: { size: 11 } },
            border: { display: false },
        },
        y: {
            grid       : { color: gridColor },
            ticks      : { color: tickColor, font: { size: 11 } },
            beginAtZero: true,
            border     : { display: false },
        },
    };

    /* ── Line chart ── */
    const trendEl = document.getElementById('trendChart');
    if (trendEl) {
        new Chart(trendEl, {
            type: 'line',
            data: {
                labels  : @json(array_column($trendData, 'label')),
                datasets: [{
                    data              : @json(array_column($trendData, 'sudah_ambil')),
                    borderColor       : '#639922',
                    backgroundColor   : 'rgba(99,153,34,0.07)',
                    fill              : true,
                    tension           : 0.45,
                    pointBackgroundColor: '#639922',
                    pointBorderColor  : isDark ? '#1f2937' : '#ffffff',
                    pointBorderWidth  : 2,
                    pointRadius       : 4,
                    borderWidth       : 2,
                }],
            },
            options: {
                responsive         : true,
                maintainAspectRatio: false,
                animation          : { duration: 1000, easing: 'easeOutQuart' },
                plugins            : { legend: { display: false }, tooltip: tooltipDefaults },
                scales             : scaleDefaults,
            },
        });
    }

   /* ── Bar chart ── */
    @if($isSuperAdmin)
        const barEl = document.getElementById('barChart');
        if (barEl) {
            new Chart(barEl, {
                type: 'bar',
                data: {
                    labels: @json(array_column($kelasData, 'nama')),
                    datasets: [
                        {
                            label: 'Sudah ambil',
                            data: @json(array_column($kelasData, 'sudah')),
                            backgroundColor: 'rgba(99,153,34,0.82)',
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                        {
                            label: 'Belum ambil',
                            data: @json(array_map(fn($k) => $k['total'] - $k['sudah'], $kelasData)),
                            backgroundColor: 'rgba(226,75,74,0.70)',
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: { legend: { display: false }, tooltip: tooltipDefaults },
                    scales: {
                        x: { ...scaleDefaults.x, grid: { display: false } },
                        y: scaleDefaults.y,
                    },
                },
            });
        }
    @endif

});
</script>

</x-filament-panels::page>
