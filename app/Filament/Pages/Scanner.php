<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Scanner extends Page
{
    // These MUST be static
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scanner';
    protected static ?string $title = 'Scanner QR MBG';
    protected static ?int $navigationSort = 5;

    // This MUST NOT be static
    protected string $view = 'filament.pages.scanner';

    public static function shouldRegisterNavigation(): bool
{
    return false;
}
}