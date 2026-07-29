<?php

namespace App\Filament\Widgets;

use App\Services\AbsensiStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AbsensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Gunakan AbsensiStatsService — sudah di-cache 60 detik,
        // sehingga data tidak dihitung ulang meski widget ini render
        // terpisah dari Dashboard::mount().
        $stats = app(AbsensiStatsService::class)->getStats($user);

        return [
            Stat::make('Total Siswa', $stats['total_siswa'])
                ->description($stats['scope'])
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Sudah Ambil MBG Hari Ini', $stats['sudah_ambil'])
                ->description('Per ' . today()->translatedFormat('d F Y'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Belum Ambil MBG', $stats['belum_ambil'])
                ->description('Siswa belum scan hari ini')
                ->icon('heroicon-o-x-circle')
                ->color($stats['belum_ambil'] > 0 ? 'danger' : 'success'),
        ];
    }
}