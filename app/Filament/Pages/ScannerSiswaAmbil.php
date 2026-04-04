<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;
class ScannerSiswaAmbil extends Page
{
    protected  string $view = 'filament.pages.scanner-siswa-ambil';
    protected static ?string $navigationLabel = 'Scanner Siswa Ambil';
    protected static string|UnitEnum|null $navigationGroup = 'Scanner MBG';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Scanner Siswa — Ambil';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}