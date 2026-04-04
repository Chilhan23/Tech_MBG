<?php
namespace App\Filament\Resources\Students\Schemas;

use App\Models\Kelas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        $user         = Auth::user();
        $isAdminKelas = $user && $user->role === 'admin' && $user->kelas_id;

        return $schema->components([
            TextInput::make('nisn')
                ->label('NISN')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(20),

            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(100),

            Select::make('jurusan')
                ->label('Jurusan')
                ->options([
                    'Rekayasa Perangkat Lunak'     => 'Rekayasa Perangkat Lunak',
                    'Teknik Komputer dan Jaringan' => 'Teknik Komputer dan Jaringan',
                    'Tehnik Jaringan Akses'        => 'Tehnik Jaringan Akses',
                    'Perfilman'                    => 'Perfilman',
                ])
                ->required(),

            Select::make('kelas_id')
                ->label('Kelas')
                ->options(
                    $isAdminKelas
                        ? Kelas::where('id', $user->kelas_id)->pluck('nama_kelas', 'id')
                        : Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')
                )
                ->default($isAdminKelas ? $user->kelas_id : null)
                ->required()
                ->disabled($isAdminKelas)
                ->dehydrated(true),

            Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options([
                    'Laki-laki' => 'Laki-laki',
                    'Perempuan' => 'Perempuan',
                ])
                ->required(),
        ]);
    }
}