<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AbsensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSiswa = Student::count();
        $sudahAmbil = Absensi::whereDate('created_at', today())->distinct('student_id')->count();
        $belumAmbil = $totalSiswa - $sudahAmbil;

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->description('Total seluruh siswa terdaftar')
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