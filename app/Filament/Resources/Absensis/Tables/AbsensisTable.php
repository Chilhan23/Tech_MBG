<?php
namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Student;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.kelas')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('student.jurusan')
                    ->label('Jurusan')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Scan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //  Filter Tanggal
                Filter::make('created_at')
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'],
                                fn (Builder $query, $date) =>
                                    $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'],
                                fn (Builder $query, $date) =>
                                    $query->whereDate('created_at', '<=', $date));
                    }),
                // Filter Jurusan
                SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->options(fn () => Student::query()
                        ->distinct()
                        ->orderBy('jurusan')
                        ->pluck('jurusan', 'jurusan')
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'],
                            fn (Builder $query, $value) =>
                                $query->whereHas('student',
                                    fn ($q) => $q->where('jurusan', $value))
                        );
                    }),
                // Filter Kelas
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(fn () => Student::query()
                        ->distinct()
                        ->orderBy('kelas')
                        ->pluck('kelas', 'kelas')
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'],
                            fn (Builder $query, $value) =>
                                $query->whereHas('student',
                                    fn ($q) => $q->where('kelas', $value))
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}