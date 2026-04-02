<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AbsensiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.nisn')
                    ->label('NISN'),
                TextEntry::make('student.name')
                    ->label('Nama Siswa'),
                TextEntry::make('student.kelas')
                    ->label('Kelas'),
                TextEntry::make('student.jurusan')
                    ->label('Jurusan'),
                TextEntry::make('created_at')
                    ->label('Waktu Scan')
                    ->dateTime(),
            ]);
    }
}
