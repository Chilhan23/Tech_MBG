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

        // Query siswa — filter kelas kalau admin
        $studentQuery = Student::query();
        if ($user && $user->role === 'admin' && $user->kelas) {
            $studentQuery->where('kelas', $user->kelas);
        }

        $totalSiswa = $studentQuery->count();

        // Query absensi — filter kelas kalau admin
        $absensiQuery = Absensi::whereDate('created_at', today());
        if ($user && $user->role === 'admin' && $user->kelas) {
            $absensiQuery->whereHas('student',
                fn ($q) => $q->where('kelas', $user->kelas)
            );
        }

        $sudahAmbil = $absensiQuery->distinct('student_id')->count();
        $belumAmbil = $totalSiswa - $sudahAmbil;

        // Label scope — superadmin lihat "semua", admin lihat kelasnya
        $scope = ($user && $user->role === 'admin' && $user->kelas)
            ? 'Kelas ' . $user->kelas
            : 'Semua Kelas';

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->description($scope)
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Sudah Ambil MBG Hari Ini', $sudahAmbil)
                ->description('Per ' . today()->format('d/m/Y'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Belum Ambil MBG', $belumAmbil)
                ->description('Siswa yang belum absen hari ini')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}