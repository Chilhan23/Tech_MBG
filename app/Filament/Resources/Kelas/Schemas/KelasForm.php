<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kelas')
                    ->required()
                    ->label('Nama Kelas')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
            ]);
    }
}
