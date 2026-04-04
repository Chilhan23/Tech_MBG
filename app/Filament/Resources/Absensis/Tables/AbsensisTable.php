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
use App\Models\Kelas;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        $user         = Auth::user();
        $isAdmin      = $user && $user->role === 'admin' && $user->kelas_id;
        $isSuperAdmin = $user && $user->role === 'superadmin';

        // ── Filter Tanggal (semua role) ──
        $filterTanggal = Filter::make('waktu_ambil')
            ->label('Tanggal')
            ->form([
                DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when($data['dari_tanggal'],
                        fn (Builder $q, $date) => $q->whereDate('waktu_ambil', '>=', $date))
                    ->when($data['sampai_tanggal'],
                        fn (Builder $q, $date) => $q->whereDate('waktu_ambil', '<=', $date));
            });

        $filters = [];

        // ── Filter tambahan hanya superadmin ──
        if ($isSuperAdmin) {
            $filters[] = SelectFilter::make('tingkat')
                ->label('Tingkat Kelas')
                ->options([
                    '10' => 'Kelas 10',
                    '11' => 'Kelas 11',
                    '12' => 'Kelas 12',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $q, $value) =>
                            $q->whereHas('student.kelas',
                                fn ($q2) => $q2->where('nama_kelas', 'like', $value . ' %'))
                    );
                });

            $filters[] = SelectFilter::make('jurusan')
                ->label('Jurusan')
                ->options(fn () => Student::query()
                    ->distinct()
                    ->orderBy('jurusan')
                    ->pluck('jurusan', 'jurusan')
                    ->toArray()
                )
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $q, $value) =>
                            $q->whereHas('student', fn ($q2) => $q2->where('jurusan', $value))
                    );
                });

            $filters[] = SelectFilter::make('kelas')
                ->label('Kelas Spesifik')
                ->options(fn () => Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray())
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $q, $value) =>
                            $q->whereHas('student', fn ($q2) => $q2->where('kelas_id', $value))
                    );
                });
        }

        $filters[] = $filterTanggal;

        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->sortable(),

                TextColumn::make('student.kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('student.jurusan')
                    ->label('Jurusan')
                    ->sortable()
                    ->visible($isSuperAdmin),

                TextColumn::make('waktu_ambil')
                    ->label('Waktu Ambil')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('waktu_kembali')
                    ->label('Waktu Kembali')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->visible($isSuperAdmin)
                    ->placeholder('Belum kembali'),
            ])
            ->filters($filters)
            ->modifyQueryUsing(function ($query) use ($isAdmin, $user) {
                if ($isAdmin) {
                    $query->whereHas('student',
                        fn ($q) => $q->where('kelas_id', $user->kelas_id)
                    );
                }
                return $query;
            })
            ->defaultSort('waktu_ambil', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                // Export PDF hanya superadmin
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->visible($isSuperAdmin)
                    ->form([
                        Select::make('tingkat')
                            ->label('Tingkat Kelas')
                            ->placeholder('— Semua Tingkat —')
                            ->options([
                                '10' => 'Kelas 10',
                                '11' => 'Kelas 11',
                                '12' => 'Kelas 12',
                            ])
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('kelas', null)),

                        Select::make('kelas')
                            ->label('Atau Kelas Spesifik')
                            ->placeholder('— Atau pilih kelas tertentu —')
                            ->options(fn () => Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'nama_kelas')->toArray())
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('tingkat', null)),

                        Select::make('jurusan')
                            ->label('Jurusan (opsional)')
                            ->placeholder('Semua Jurusan')
                            ->options(fn () => Student::query()
                                ->distinct()
                                ->orderBy('jurusan')
                                ->pluck('jurusan', 'jurusan')
                                ->toArray()
                            ),

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