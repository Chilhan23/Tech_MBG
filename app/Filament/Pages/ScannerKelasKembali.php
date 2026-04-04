<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;
class ScannerKelasKembali extends Page
{
    protected  string $view = 'filament.pages.scanner-kelas-kembali';
    protected static ?string $navigationLabel = 'Scanner Kelas Kembali';
    protected static string|UnitEnum|null $navigationGroup = 'Scanner MBG';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Scanner Kelas — Kembalikan Ompreng';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}