<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nisn')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Select::make('jurusan')
                    ->options([
                        'Rekayasa Perangkat Lunak' => 'Rekayasa Perangkat Lunak',
                        'Teknik Komputer dan Jaringan' => 'Teknik Komputer dan Jaringan',
                        'Tehnik Jaringan Akses' => 'Tehnik Jaringan Akses',
                        'Perfilman' => 'Perfilman',
                    ])  
                    ->required()
                    ->searchable(),
                Select::make('kelas')
                    ->options([
                        '10 BP' => '10 BP ',
                        '10 TJKT 1' => '10 TJKT 1',
                        '10 TJKT 2' => '10 TJKT 2',
                        '10 TJKT 3' => '10 TJKT 3',
                        '10 PPLG 1' => '10 PPLG 1',
                        '10 PPLG 2' => '10 PPLG 2',
                        '10 PPLG 3' => '10 PPLG 3',
                        '11 PF 1' => '11 PF 1',
                        '11 PF 2' => '11 PF 2',
                        '11 RPL 1' => '11 RPL 1',
                        '11 RPL 2' => '11 RPL 2',
                        '11 RPL 3' => '11 RPL 3',
                        '11 TKJ 1' => '11 TKJ 1',
                        '11 TKJ 2' => '11 TKJ 2',
                        '11 TJA 1' => '11 TJA 1',
                        '11 TJA 2' => '11 TJA 2',
                        '12 PF 1' => '12 PF 1',
                        '12 PF 2' => '12 PF 2',
                        '12 RPL 1' => '12 RPL 1',
                        '12 RPL 2' => '12 RPL 2',
                        '12 RPL 3' => '12 RPL 3',
                        '12 TKJ 1' => '12 TKJ 1',
                        '12 TKJ 2' => '12 TKJ 2',
                        '12 TJA ' => '12 TJA ',
                    ])  
                    ->required()
                    ->searchable(),
                Select::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->required()
                    ->searchable(),
            ]);
    }
}
