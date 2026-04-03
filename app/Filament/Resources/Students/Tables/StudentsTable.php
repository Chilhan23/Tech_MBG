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
use Filament\Actions\BulkAction;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class StudentsTable
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

    private static function kelasOptions(): array
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin' && $user->kelas) {
            return [$user->kelas => $user->kelas];
        }

        return self::KELAS_OPTIONS;
    }

    public static function configure(Table $table): Table
    {
        $toolbarActions = [];

        $toolbarActions[] = Action::make('import')
            ->label('Masukan Data Siswa/i Lewat Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                FileUpload::make('file')
                    ->label('Pastikan file Excel memiliki kolom: NISN, Nama, Jurusan, Kelas, Jenis Kelamin')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ]),
            ])
            ->action(function (array $data) {
                $import = new UserImport();
                Excel::import($import, $data['file']);

                if (count($import->rejected) > 0) {
                    Notification::make()
                        ->title('Beberapa baris ditolak (kelas tidak sesuai)')
                        ->body(implode("\n", $import->rejected))
                        ->danger()
                        ->send();
                } elseif (count($import->duplicates) > 0) {
                    Notification::make()
                        ->title('Duplicate ditemukan')
                        ->body(implode("\n", $import->duplicates))
                        ->warning()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Data berhasil diimpor')
                        ->success()
                        ->send();
                }
            });

        $toolbarActions[] = BulkActionGroup::make([
            BulkAction::make('bulk_print_qr')
                ->label('Cetak QR Terpilih')
                ->icon('heroicon-o-printer')
                ->action(function (Collection $records) {
                    $ids = $records->pluck('id')->join(',');
                    redirect()->away(route('students.qr.bulk-print', ['ids' => $ids]));
                })
                ->deselectRecordsAfterCompletion(),
            DeleteBulkAction::make(),
        ]);

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
                SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->options([
                        'Rekayasa Perangkat Lunak'     => 'Rekayasa Perangkat Lunak',
                        'Teknik Komputer dan Jaringan' => 'Teknik Komputer dan Jaringan',
                        'Tehnik Jaringan Akses'        => 'Tehnik Jaringan Akses',
                        'Perfilman'                    => 'Perfilman',
                    ]),
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(self::kelasOptions()),
            ])
            ->recordActions([
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code Siswa')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString('
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;">
                            <div style="padding:15px;background:white;border-radius:10px;margin-bottom:20px;">
                                ' . \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($record->nisn) . '
                            </div>
                            <h3 style="font-size:1.25rem;font-weight:bold;margin-bottom:5px;">' . $record->name . '</h3>
                            <p style="color:gray;font-size:1rem;">' . $record->nisn . '</p>
                            <p style="color:gray;font-size:0.9rem;">' . $record->kelas . ' ' . $record->jurusan . '</p>
                        </div>
                    ')),
                Action::make('print_qr')
                    ->label('Cetak Kartu')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record): string => route('students.qr.print', ['student' => $record]))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions($toolbarActions);
    }
}