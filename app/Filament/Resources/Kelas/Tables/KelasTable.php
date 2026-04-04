<?php
namespace App\Filament\Resources\Kelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelas')
                    ->searchable(),
                TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->sortable(),
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
                Action::make('qr_kelas')
                    ->label('QR Kelas')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code Kelas')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString('
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;">
                            <div style="padding:15px;background:white;border-radius:10px;margin-bottom:20px;border:1px solid #e2e8f0;">
                                ' . \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->color(0, 104, 179)->generate($record->nama_kelas) . '
                            </div>
                            <h3 style="font-size:1.25rem;font-weight:bold;margin-bottom:5px;">' . $record->nama_kelas . '</h3>
                            <p style="color:gray;font-size:0.9rem;">' . $record->students_count . ' Siswa</p>
                        </div>
                    ')),

                Action::make('print_qr_kelas')
                    ->label('Cetak QR')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record): string => route('kelas.qr.print', ['kelas' => $record]))
                    ->openUrlInNewTab(),

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