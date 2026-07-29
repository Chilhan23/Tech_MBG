<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AbsensiStatsWidget;
use App\Filament\Widgets\AbsensiPerKelasWidget;
use App\Services\AbsensiStatsService;
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
        $this->greeting     = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default    => 'Selamat malam',
        };

        // Delegasikan semua kalkulasi ke AbsensiStatsService.
        // Service sudah handle caching (60 detik) dan query yang memanfaatkan
        // composite index (waktu_ambil, student_id).
        $service = app(AbsensiStatsService::class);

        $this->stats     = $service->getStats($user);
        $this->kelasData = $service->getKelasData($user);
        $this->trendData = $service->getTrendData($user);
    }
}