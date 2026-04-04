<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;  
use Illuminate\Support\Facades\Auth;        

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Data Siswa/i';
    protected static ?string $modelLabel = 'Data Siswa/i';
    protected static ?string $pluralModelLabel = 'Data Siswa/i';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen MBG';
    protected static ?int $navigationSort = 10;
    

    // ── Filter data berdasarkan role user ────────────────────
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        // Admin kelas hanya lihat siswa kelasnya sendiri
        if ($user && $user->role === 'admin' && $user->kelas) {
           $query->whereHas('kelas', function ($q) use ($user) {
                $q->where('nama_kelas', $user->kelas);
            });
        }

        // Superadmin → tidak ada filter, lihat semua
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view'   => ViewStudent::route('/{record}'),
            'edit'   => EditStudent::route('/{record}/edit'),
        ];
    }
}