<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Kelas; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form; 

class UserForm
{
    public static function configure($form)
    {
        return $form->schema([

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

            Select::make('kelas_id')
                ->label('Kelas yang Dikelola')
                ->options(Kelas::all()->pluck('nama_kelas', 'id')->toArray()) 
                ->searchable()
                ->unique(ignoreRecord: true)
                ->placeholder('Pilih kelas...')
                ->required(fn ($get) => $get('role') === 'admin')
                ->visible(fn ($get) => $get('role') === 'admin')
                ->helperText('Wajib diisi untuk Admin Kelas'),

        ]);
    }
}