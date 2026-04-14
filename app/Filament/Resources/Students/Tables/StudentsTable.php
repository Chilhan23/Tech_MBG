<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Kelas;
use App\Models\Student;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        $user    = Auth::user();
        
        // Cek admin berdasarkan role dan ketersediaan data kelas di user
        $isAdmin = $user && $user->role === 'admin' && $user->kelas;

        $toolbarActions = [];

        // --- Action Import Excel ---
        $toolbarActions[] = Action::make('import')
            ->label('Masukan Data Siswa/i Lewat Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->button()
            ->color('info')
            ->form([
                FileUpload::make('file')
                    ->label('Format: NISN, Nama, Jurusan, Kelas, Jenis Kelamin')
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
                        ->title('Data ditolak (Kelas tidak sesuai)')
                        ->body(implode(", ", $import->rejected))
                        ->danger()
                        ->send();
                } elseif (count($import->duplicates) > 0) {
                    Notification::make()
                        ->title('NISN Duplikat Terdeteksi')
                        ->body(implode(", ", $import->duplicates))
                        ->warning()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Data Berhasil Diimpor')
                        ->success()
                        ->send();
                }
            });

        // --- Bulk Actions ---
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

        // --- Filters ---
        $filters = [];
        if (!$isAdmin) {
            $filters[] = SelectFilter::make('jurusan')
                ->label('Jurusan')
                ->options([
                    'Rekayasa Perangkat Lunak'     => 'Rekayasa Perangkat Lunak',
                    'Teknik Komputer dan Jaringan' => 'Teknik Komputer dan Jaringan',
                    'Tehnik Jaringan Akses'        => 'Tehnik Jaringan Akses',
                    'Perfilman'                    => 'Perfilman',
                ]);

            $filters[] = SelectFilter::make('kelas_id')
                ->label('Kelas')
                ->relationship('kelas', 'nama_kelas');
        }

        return $table
            ->columns([
                TextColumn::make('nisn')
                    ->copyable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jurusan')
                    ->searchable(),
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters($filters)
            ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $user) {
                if ($isAdmin) {
                    // Filter berdasarkan nama_kelas di tabel relasi kelas
                    $query->whereHas('kelas', function ($q) use ($user) {
                        $q->where('nama_kelas', $user->kelas);
                    });
                }
                return $query;
            })
            ->recordActions([
                // --- Modal QR Code ---
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code Siswa')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn ($record) => new HtmlString('
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;text-align:center;">
                            <div style="padding:15px;background:white;border-radius:10px;margin-bottom:20px;box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                                ' . QrCode::size(250)->generate($record->nisn) . '
                            </div>
                            <h3 style="font-size:1.25rem;font-weight:bold;margin-bottom:5px;">' . $record->name . '</h3>
                            <p style="color:gray;font-size:1rem;margin-bottom:5px;">' . $record->nisn . '</p>
                            <p style="color:gray;font-size:0.9rem;">' . ($record->kelas?->nama_kelas ?? '-') . ' — ' . $record->jurusan . '</p>
                        </div>
                    ')),
                
                // --- Print Kartu ---
                Action::make('print_qr')
                    ->label('Cetak Kartu')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record): string => route('students.qr.print', ['student' => $record]))
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions($toolbarActions);
    }
}