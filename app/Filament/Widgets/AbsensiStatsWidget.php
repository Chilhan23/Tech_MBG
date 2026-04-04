<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AbsensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdminKelas = $user && $user->role === 'admin' && $user->kelas->nama_kelas;

        // 1. Query Total Siswa — Filter lewat relasi kelas
        $studentQuery = Student::query();
        if ($isAdminKelas) {
            $studentQuery->whereHas('kelas', function ($q) use ($user) {
                $q->where('id', $user->kelas_id);
            });
        }
        $totalSiswa = $studentQuery->count();

        // 2. Query Absensi Hari Ini — Filter lewat relasi berantai (Absensi -> Student -> Kelas)
        $absensiQuery = Absensi::whereDate('created_at', today());
        if ($isAdminKelas) {
            $absensiQuery->whereHas('student.kelas', function ($q) use ($user) {
                $q->where('id', $user->kelas_id);
            });
        }

        $sudahAmbil = $absensiQuery->distinct('student_id')->count();
        $belumAmbil = max(0, $totalSiswa - $sudahAmbil); // Pakai max(0) biar gak minus kalau ada anomali data

        // 3. Label Scope
        $scope = $isAdminKelas ? 'Kelas ' . $user->kelas->nama_kelas : 'Semua Kelas';

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->description($scope)
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Sudah Ambil MBG Hari Ini', $sudahAmbil)
                ->description('Per ' . today()->translatedFormat('d F Y'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Belum Ambil MBG', $belumAmbil)
                ->description('Siswa belum scan hari ini')
                ->icon('heroicon-o-x-circle')
                ->color($belumAmbil > 0 ? 'danger' : 'success'),
        ];
    }
}