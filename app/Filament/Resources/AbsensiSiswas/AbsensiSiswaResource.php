<?php

namespace App\Filament\Resources\AbsensiSiswas;

use App\Filament\Resources\AbsensiSiswas\Pages\ManageAbsensiSiswas;
use App\Models\Absensi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Absensi Siswa';
    protected static ?string $modelLabel = 'Absensi Siswa';
    protected static ?string $pluralModelLabel = 'Absensi Siswa';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen MBG';
    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AbsensiSiswaTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAbsensiSiswas::route('/'),
        ];
    }
}