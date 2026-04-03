<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Resources\Absensis\Pages\ViewAbsensi;
use App\Filament\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Resources\Absensis\Schemas\AbsensiInfolist;
use App\Filament\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Absensi MBG';
    protected static ?string $modelLabel = 'Absensi MBG';
    protected static ?string $pluralModelLabel = 'Absensi MBG';

    // ── Filter data berdasarkan role user ────────────────────
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        // Admin kelas hanya lihat absensi siswa di kelasnya
        if ($user && $user->role === 'admin' && $user->kelas) {
            $query->whereHas('student',
                fn (Builder $q) => $q->where('kelas', $user->kelas)
            );
        }

        // Superadmin → tidak ada filter, lihat semua
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AbsensiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAbsensis::route('/'),
            'create' => CreateAbsensi::route('/create'),
            'view'   => ViewAbsensi::route('/{record}'),
            'edit'   => EditAbsensi::route('/{record}/edit'),
        ];
    }
}