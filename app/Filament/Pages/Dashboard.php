<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AbsensiStatsWidget;
use App\Filament\Widgets\AbsensiPerKelasWidget;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Student;
use Filament\Pages\Page;
use BackedEnum;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public array $stats     = [];
    public array $trendData = [];
    public array $kelasData = [];
    public bool  $isSuperAdmin = false;
    public string $greeting = '';
    public string $userName = '';

    public function mount(): void
    {
        $user = Auth::user();
        $hour = now()->hour;

        $this->isSuperAdmin = $user->role === 'superadmin';
        $this->userName     = $user->name;
        $this->greeting     = match(true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default    => 'Selamat malam',
        };

        // Query total siswa
        $studentQ = Student::query();
        if (!$this->isSuperAdmin) {
            $studentQ->where('kelas_id', $user->kelas_id);
        }
        $totalSiswa = $studentQ->count();

        // Query absensi hari ini
        $absensiQ = Absensi::whereDate('waktu_ambil', today());
        if (!$this->isSuperAdmin) {
            $absensiQ->whereHas('student', fn($q) => $q->where('kelas_id', $user->kelas_id));
        }
        $sudahAmbil = $absensiQ->distinct('student_id')->count();

        $this->stats = [
            'total_siswa' => $totalSiswa,
            'sudah_ambil' => $sudahAmbil,
            'belum_ambil' => max(0, $totalSiswa - $sudahAmbil),
            'persen'      => $totalSiswa > 0 ? round($sudahAmbil / $totalSiswa * 100, 1) : 0,
            'scope'       => $this->isSuperAdmin ? 'Semua kelas' : 'Kelas ' . $user->kelas->nama_kelas,
            'tanggal'     => now()->translatedFormat('l, d F Y'),
        ];

        // Data per kelas - pisah selectRaw, hindari konflik withCount + selectRaw('*')
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
                AND DATE(absensis.waktu_ambil) = CURDATE()
            ), 0) as sudah')
            ->orderBy('nama_kelas');

        if (!$this->isSuperAdmin) {
            $kelasQuery->where('kelas.id', $user->kelas_id);
        }

        $this->kelasData = $kelasQuery->get()
            ->map(fn($k) => [
                'nama'  => $k->nama_kelas,
                'total' => (int) ($k->total ?? 0),
                'sudah' => (int) ($k->sudah ?? 0),
            ])->toArray();

        // Tren 7 hari
        $this->trendData = collect(range(6, 0))->map(function ($ago) use ($user) {
            $date = today()->subDays($ago);
            $q    = Absensi::whereDate('waktu_ambil', $date);
            if (!$this->isSuperAdmin) {
                $q->whereHas('student', fn($q2) => $q2->where('kelas_id', $user->kelas_id));
            }
            return [
                'label'       => $date->translatedFormat('D'),
                'sudah_ambil' => $q->distinct('student_id')->count(),
            ];
        })->values()->toArray();
    }
}