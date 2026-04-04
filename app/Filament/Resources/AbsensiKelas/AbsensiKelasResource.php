<?php

namespace App\Filament\Resources\AbsensiKelas;

use App\Filament\Resources\AbsensiKelas\Pages\ManageAbsensiKelas;
use App\Models\KelasLog;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
class AbsensiKelasResource extends Resource
{
    protected static ?string $model = KelasLog::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?string $navigationLabel = 'Absensi Kelas';
    protected static ?string $modelLabel = 'Absensi Kelas';
    protected static ?string $pluralModelLabel = 'Absensi Kelas';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen MBG';
    protected static ?int $navigationSort = 21;

    public static function canAccess(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

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
        return AbsensiKelasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAbsensiKelas::route('/'),
        ];
    }
}