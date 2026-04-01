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
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code Siswa')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString('
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                            <div style="padding: 15px; background: white; border-radius: 10px; margin-bottom: 20px;">
                                ' . \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate(route('scanner.public_scan', ['nisn' => $record->nisn], true)) . '
                            </div>
                            <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 5px;">'.$record->name.'</h3>
                            <p style="color: gray; font-size: 1rem;">'.$record->nisn.'</p>
                            <p style="color: gray; font-size: 0.9rem;">'.$record->kelas.' '.$record->jurusan.'</p>
                        </div>
                    ')),
                Action::make('print_qr')
                    ->label('Cetak QR')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record): string => route('students.qr.print', ['student' => $record]))
                    ->openUrlInNewTab(),
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
                        $import = new UserImport(); 

                            Excel::import($import, $data['file']);

                            if (count($import->duplicates) > 0) {
                                Notification::make()
                                    ->title('Duplicate ditemukan')
                                    ->body(implode("\n", $import->duplicates))
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Data berhasil diimpor')
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
