<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
class ScannerKelasAmbil extends Page
{
    protected  string $view = 'filament.pages.scanner-kelas-ambil';
    protected static ?string $navigationLabel = 'Scanner Kelas Ambil';
    protected static string|UnitEnum|null $navigationGroup = 'Scanner MBG';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Scanner Kelas — Ambil Ompreng';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}