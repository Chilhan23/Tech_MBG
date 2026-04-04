<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;
class ScannerSiswaKembali extends Page
{
    protected  string $view = 'filament.pages.scanner-siswa-kembali';
    protected static ?string $navigationLabel = 'Scanner Siswa Kembali';
    protected static string|UnitEnum|null $navigationGroup = 'Scanner MBG';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Scanner Siswa — Kembali';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}