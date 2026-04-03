<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
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
        return $schema->components([

            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->helperText('Kosongkan jika tidak ingin mengubah password'),

            Select::make('role')
                ->label('Role')
                ->options([
                    'superadmin' => 'Super Admin',
                    'admin'      => 'Admin Kelas',
                ])
                ->default('admin')
                ->required()
                ->live(),

            Select::make('kelas')
                ->label('Kelas yang Dikelola')
                ->options(self::KELAS_OPTIONS)
                ->placeholder('Pilih kelas...')
                ->required(fn ($get) => $get('role') === 'admin')
                ->visible(fn ($get) => $get('role') === 'admin')
                ->helperText('Wajib diisi untuk Admin Kelas'),

        ]);
    }
}