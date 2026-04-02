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
use Filament\Actions\Action;

class AbsensisTable
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

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->sortable(),
                TextColumn::make('student.kelas')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('student.jurusan')
                    ->label('Jurusan')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Ambil MBG')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([

                // Filter Tingkat (10 / 11 / 12)
                SelectFilter::make('tingkat')
                    ->label('Tingkat Kelas')
                    ->options([
                        '10' => 'Kelas 10 (Semua)',
                        '11' => 'Kelas 11 (Semua)',
                        '12' => 'Kelas 12 (Semua)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'],
                            fn (Builder $query, $value) =>
                                $query->whereHas('student',
                                    fn ($q) => $q->where('kelas', 'like', $value . ' %')
                                )
                        );
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

                // Filter Kelas spesifik
                SelectFilter::make('kelas')
                    ->label('Kelas Spesifik')
                    ->options(self::KELAS_OPTIONS)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'],
                            fn (Builder $query, $value) =>
                                $query->whereHas('student',
                                    fn ($q) => $q->where('kelas', $value))
                        );
                    }),

                // Filter Tanggal
                Filter::make('created_at')
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
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

            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([

                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->form([

                        // Filter 1: Tingkat — saat dipilih, kelas spesifik di-reset
                        Select::make('tingkat')
                            ->label('Tingkat Kelas')
                            ->placeholder('— Pilih tingkat —')
                            ->options([
                                '10' => 'Kelas 10 (semua kelas 10)',
                                '11' => 'Kelas 11 (semua kelas 11)',
                                '12' => 'Kelas 12 (semua kelas 12)',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('kelas', null)),

                        // Filter 2: Kelas spesifik — saat dipilih, tingkat di-reset
                        Select::make('kelas')
                            ->label('Atau Kelas Spesifik')
                            ->placeholder('— Atau pilih kelas tertentu —')
                            ->options(self::KELAS_OPTIONS)
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('tingkat', null)),

                        // Filter 3: Jurusan — bisa dikombinasi dengan 2 filter di atas
                        Select::make('jurusan')
                            ->label('Jurusan (opsional)')
                            ->placeholder('Semua Jurusan')
                            ->options(fn () => Student::query()
                                ->distinct()
                                ->orderBy('jurusan')
                                ->pluck('jurusan', 'jurusan')
                                ->toArray()
                            ),

                        // Tanggal — wajib isi
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now()->toDateString())
                            ->required(),

                    ])
                    ->action(function (array $data) {
                        $params = http_build_query(array_filter([
                            'tingkat' => $data['tingkat'] ?? '',
                            'kelas'   => $data['kelas']   ?? '',
                            'jurusan' => $data['jurusan'] ?? '',
                            'tanggal' => $data['tanggal'] ?? now()->toDateString(),
                        ]));

                        redirect()->away(route('absensi.export-pdf') . '?' . $params);
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}