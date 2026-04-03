<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StudentForm
{
    const KELAS_OPTIONS = [
        '10 BP'     => '10 BP',
        '10 TJKT 1' => '10 TJKT 1',
        '10 TJKT 2' => '10 TJKT 2',
        '10 TJKT 3' => '10 TJKT 3',
        '10 PPLG 1' => '10 PPLG 1',
        '10 PPLG 2' => '10 PPLG 2',
        '10 PPLG 3' => '10 PPLG 3',
        '11 PF 1'   => '11 PF 1',
        '11 PF 2'   => '11 PF 2',
        '11 RPL 1'  => '11 RPL 1',
        '11 RPL 2'  => '11 RPL 2',
        '11 RPL 3'  => '11 RPL 3',
        '11 TKJ 1'  => '11 TKJ 1',
        '11 TKJ 2'  => '11 TKJ 2',
        '11 TJA 1'  => '11 TJA 1',
        '11 TJA 2'  => '11 TJA 2',
        '12 PF 1'   => '12 PF 1',
        '12 PF 2'   => '12 PF 2',
        '12 RPL 1'  => '12 RPL 1',
        '12 RPL 2'  => '12 RPL 2',
        '12 RPL 3'  => '12 RPL 3',
        '12 TKJ 1'  => '12 TKJ 1',
        '12 TKJ 2'  => '12 TKJ 2',
        '12 TJA'    => '12 TJA',
    ];

    public static function configure(Schema $schema): Schema
    {
        $user         = Auth::user();
        $isAdminKelas = $user && $user->role === 'admin' && $user->kelas;

        // Admin kelas: option kelas hanya miliknya, otomatis terpilih
        $kelasOptions = $isAdminKelas
            ? [$user->kelas => $user->kelas]
            : self::KELAS_OPTIONS;

        $kelasDefault = $isAdminKelas ? $user->kelas : null;

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

            Select::make('kelas')
                ->label('Kelas')
                ->options($kelasOptions)
                ->default($kelasDefault)
                ->required()
                // Kalau admin kelas, field ini disabled (tidak bisa ganti)
                ->disabled($isAdminKelas)
                ->dehydrated(true), // tetap tersimpan meski disabled

            Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options([
                    'Laki-laki'  => 'Laki-laki',
                    'Perempuan'  => 'Perempuan',
                ])
                ->required(),

        ]);
    }
}