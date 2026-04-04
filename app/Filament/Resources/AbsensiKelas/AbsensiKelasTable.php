<?php

namespace App\Filament\Resources\AbsensiKelas;

use App\Models\KelasLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component as Livewire;

class AbsensiKelasTable
{
    public static function configure(Table $table): Table
    {
        $user    = Auth::user();
        $isAdmin = $user?->role === 'admin' && $user->kelas_id;

        return $table
            ->columns([
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')->sortable()->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('diambil')
                    ->label('Waktu Ambil')->dateTime('H:i')
                    ->placeholder('Belum diambil')->sortable(),
                TextColumn::make('dikembalikan')
                    ->label('Waktu Kembali')->dateTime('H:i')
                    ->placeholder('Belum dikembalikan')->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (KelasLog $record): string =>
                        $record->dikembalikan ? 'Selesai'
                        : ($record->diambil ? 'Sedang Makan' : 'Belum Ambil')
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'Selesai'      => 'success',
                        'Sedang Makan' => 'warning',
                        default        => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['dari_tanggal'] ?? null,
                            fn ($q2, $d) => $q2->whereDate('tanggal', '>=', $d))
                        ->when($data['sampai_tanggal'] ?? null,
                            fn ($q2, $d) => $q2->whereDate('tanggal', '<=', $d))
                    ),
            ])
            ->modifyQueryUsing(function (Builder $query, Livewire $livewire) use ($isAdmin, $user) {
                if ($isAdmin) {
                    $query->where('kelas_id', $user->kelas_id);
                }
                $filterData = $livewire->tableFilters['tanggal'] ?? [];
                $adaFilter  = !empty($filterData['dari_tanggal']) || !empty($filterData['sampai_tanggal']);
                if (!$adaFilter) {
                    $query->whereDate('tanggal', today());
                }
            })
            ->recordActions([ViewAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}