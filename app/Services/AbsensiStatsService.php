<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * AbsensiStatsService
 *
 * Single source of truth untuk semua kalkulasi statistik absensi MBG.
 * Semua method menggunakan Cache::remember(60s) agar tidak dihitung ulang
 * oleh multiple widgets / user berbeda dalam satu menit.
 *
 * Query menggunakan whereBetween(startOfDay, endOfDay) — bukan whereDate()
 * — supaya composite index (waktu_ambil, student_id) bisa dimanfaatkan.
 */
class AbsensiStatsService
{
    /**
     * Kembalikan statistik harian (total siswa, sudah ambil, belum ambil, persen, scope).
     */
    public function getStats(User $user): array
    {
        $isSuperAdmin = $user->role === 'superadmin';
        $kelasId      = $user->kelas_id;
        $cacheKey     = 'absensi_stats_' . ($isSuperAdmin ? 'all' : "kelas_{$kelasId}") . '_' . today()->toDateString();

        return Cache::remember($cacheKey, 60, function () use ($isSuperAdmin, $kelasId, $user) {
            // ── Total Siswa ──────────────────────────────────────────────────
            $studentQ = Student::query();
            if (! $isSuperAdmin) {
                $studentQ->where('kelas_id', $kelasId);
            }
            $totalSiswa = $studentQ->count();

            // ── Sudah Ambil Hari Ini ─────────────────────────────────────────
            // Pakai whereBetween agar index (waktu_ambil, student_id) terpakai.
            $start = today()->startOfDay();
            $end   = today()->endOfDay();

            $absensiQ = Absensi::whereBetween('waktu_ambil', [$start, $end]);
            if (! $isSuperAdmin) {
                $absensiQ->whereHas('student', fn ($q) => $q->where('kelas_id', $kelasId));
            }
            $sudahAmbil = $absensiQ->distinct('student_id')->count();

            return [
                'total_siswa' => $totalSiswa,
                'sudah_ambil' => $sudahAmbil,
                'belum_ambil' => max(0, $totalSiswa - $sudahAmbil),
                'persen'      => $totalSiswa > 0 ? round($sudahAmbil / $totalSiswa * 100, 1) : 0,
                'scope'       => $isSuperAdmin ? 'Semua kelas' : 'Kelas ' . optional($user->kelas)->nama_kelas,
                'tanggal'     => now()->translatedFormat('l, d F Y'),
            ];
        });
    }

    /**
     * Kembalikan rekap absensi per kelas hari ini.
     * Subquery menggunakan BETWEEN untuk memanfaatkan index.
     */
    public function getKelasData(User $user): array
    {
        $isSuperAdmin = $user->role === 'superadmin';
        $kelasId      = $user->kelas_id;
        $cacheKey     = 'absensi_kelas_data_' . ($isSuperAdmin ? 'all' : "kelas_{$kelasId}") . '_' . today()->toDateString();

        return Cache::remember($cacheKey, 60, function () use ($isSuperAdmin, $kelasId) {
            // Gunakan binding parameter untuk start/end of day agar index terpakai.
            $start = today()->startOfDay()->toDateTimeString();
            $end   = today()->endOfDay()->toDateTimeString();

            $kelasQuery = Kelas::select('kelas.id', 'kelas.nama_kelas')
                ->selectRaw('(
                    SELECT COUNT(students.id)
                    FROM students
                    WHERE students.kelas_id = kelas.id
                ) as total')
                ->selectRaw('COALESCE((
                    SELECT COUNT(DISTINCT absensis.student_id)
                    FROM absensis
                    JOIN students ON absensis.student_id = students.id
                    WHERE students.kelas_id = kelas.id
                    AND absensis.waktu_ambil BETWEEN ? AND ?
                ), 0) as sudah', [$start, $end])
                ->orderBy('nama_kelas');

            if (! $isSuperAdmin) {
                $kelasQuery->where('kelas.id', $kelasId);
            }

            return $kelasQuery->get()
                ->map(fn ($k) => [
                    'nama'  => $k->nama_kelas,
                    'total' => (int) ($k->total ?? 0),
                    'sudah' => (int) ($k->sudah ?? 0),
                ])->toArray();
        });
    }

    /**
     * Kembalikan data trend 7 hari terakhir menggunakan SATU query + groupBy,
     * bukan 7 query terpisah.
     */
    public function getTrendData(User $user): array
    {
        $isSuperAdmin = $user->role === 'superadmin';
        $kelasId      = $user->kelas_id;
        $cacheKey     = 'absensi_trend_' . ($isSuperAdmin ? 'all' : "kelas_{$kelasId}") . '_' . today()->toDateString();

        return Cache::remember($cacheKey, 60, function () use ($isSuperAdmin, $kelasId) {
            $start = today()->subDays(6)->startOfDay();
            $end   = today()->endOfDay();

            // Satu query untuk 7 hari sekaligus
            $query = Absensi::whereBetween('waktu_ambil', [$start, $end])
                ->selectRaw('DATE(waktu_ambil) as hari, COUNT(DISTINCT student_id) as sudah_ambil')
                ->groupByRaw('DATE(waktu_ambil)');

            if (! $isSuperAdmin) {
                $query->whereHas('student', fn ($q) => $q->where('kelas_id', $kelasId));
            }

            // Key by date string untuk O(1) lookup
            $rows = $query->get()->keyBy('hari');

            // Map 7 hari ke format yang diharapkan view
            return collect(range(6, 0))->map(function ($ago) use ($rows) {
                $date    = today()->subDays($ago);
                $dateStr = $date->toDateString();

                return [
                    'label'       => $date->translatedFormat('D'),
                    'sudah_ambil' => (int) ($rows[$dateStr]->sudah_ambil ?? 0),
                ];
            })->values()->toArray();
        });
    }

    /**
     * Invalidate semua cache stats untuk hari ini.
     * Panggil ini ketika ada scan absensi baru masuk.
     */
    public function invalidateCache(?int $kelasId = null): void
    {
        $date = today()->toDateString();

        // Invalidate cache all (superadmin)
        Cache::forget("absensi_stats_all_{$date}");
        Cache::forget("absensi_kelas_data_all_{$date}");
        Cache::forget("absensi_trend_all_{$date}");

        // Invalidate cache kelas spesifik
        if ($kelasId) {
            Cache::forget("absensi_stats_kelas_{$kelasId}_{$date}");
            Cache::forget("absensi_kelas_data_kelas_{$kelasId}_{$date}");
            Cache::forget("absensi_trend_kelas_{$kelasId}_{$date}");
        }
    }
}
