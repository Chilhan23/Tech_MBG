<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable(),

                TextColumn::make('student.name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('student.kelas')
                    ->label('Kelas'),

                TextColumn::make('student.jurusan')
                    ->label('Jurusan'),

                TextColumn::make('created_at')
                    ->label('Waktu Ambil')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
