<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Filament\Notifications\Notification;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('jurusan')
                    ->searchable(),
                TextColumn::make('kelas')
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                 Action::make('import')
                    ->label('Masukan Data Siswa/i Lewat Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pastikan file Excel Anda memiliki format yang benar dengan kolom: NISN, Nama, Jurusan, Kelas, Jenis Kelamin')
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $import = new UserImport(); // bikin instance

                            Excel::import($import, $data['file']);

                            if (count($import->duplicates) > 0) {
                                Notification::make()
                                    ->title('Duplicate ditemukan')
                                    ->body(implode("\n", $import->duplicates))
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import berhasil')
                                    ->success()
                                    ->send();
                            }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
