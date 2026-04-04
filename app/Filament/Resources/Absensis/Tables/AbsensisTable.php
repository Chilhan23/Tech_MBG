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
use App\Models\Kelas; // Tambahkan ini
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        $user    = Auth::user();
        $isAdmin = $user && $user->role === 'admin' && $user->kelas;

        // Filter tanggal
        $filterTanggal = Filter::make('created_at')
            ->label('Tanggal')
            ->form([
                DatePicker::make('created_from')->label('Dari Tanggal'),
                DatePicker::make('created_until')->label('Sampai Tanggal'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when($data['created_from'],
                        fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                    ->when($data['created_until'],
                        fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
            });

        $filters = [];

        if (!$isAdmin) {
            // Filter Tingkat - Cari di nama_kelas tabel kelas
            $filters[] = SelectFilter::make('tingkat')
                ->label('Tingkat Kelas')
                ->options([
                    '10' => 'Kelas 10',
                    '11' => 'Kelas 11',
                    '12' => 'Kelas 12',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $query, $value) =>
                            $query->whereHas('student.kelas', // Ubah ini
                                fn ($q) => $q->where('nama_kelas', 'like', $value . ' %')
                            )
                    );
                });

            $filters[] = SelectFilter::make('jurusan')
                ->label('Jurusan')
                ->options(fn () => Student::query()->distinct()->orderBy('jurusan')->pluck('jurusan', 'jurusan')->toArray())
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $query, $value) =>
                            $query->whereHas('student', fn ($q) => $q->where('jurusan', $value))
                    );
                });

            // Filter Kelas Spesifik - Ambil dari database, jangan hardcode
            $filters[] = SelectFilter::make('kelas')
                ->label('Kelas Spesifik')
                ->options(fn () => Kelas::pluck('nama_kelas', 'nama_kelas')->toArray())
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'],
                        fn (Builder $query, $value) =>
                            $query->whereHas('student.kelas', // Ubah ini
                                fn ($q) => $q->where('nama_kelas', $value))
                    );
                });
        }

        $filters[] = $filterTanggal;

        return $table
            ->columns([
                TextColumn::make('student.name')->label('Nama Siswa')->searchable()->sortable(),
                TextColumn::make('student.nisn')->label('NISN')->sortable(),
                // Ini sudah benar pakai dot notation
                TextColumn::make('student.kelas.nama_kelas')->label('Kelas')->sortable(),
                TextColumn::make('student.jurusan')->label('Jurusan')->sortable(),
                TextColumn::make('created_at')->label('Waktu Ambil MBG')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters($filters)
            ->modifyQueryUsing(function ($query) use ($isAdmin, $user) {
                if ($isAdmin) {
                    // ERROR DI SINI TADI: Harus tembus ke relasi kelas
                    $query->whereHas('student.kelas',
                        fn ($q) => $q->where('nama_kelas', $user->kelas)
                    );
                }
                return $query;
            })
            ->recordActions([ViewAction::make()])
            ->toolbarActions([
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->form([
                        Select::make('tingkat')
                            ->options(['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12'])
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('kelas', null)),
                        Select::make('kelas')
                            ->label('Atau Kelas Spesifik')
                            // Ambil dari database biar konsisten
                            ->options(fn () => Kelas::pluck('nama_kelas', 'nama_kelas')->toArray())
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('tingkat', null)),
                        Select::make('jurusan')
                            ->options(fn () => Student::query()->distinct()->pluck('jurusan', 'jurusan')->toArray()),
                        DatePicker::make('tanggal')->default(now()->toDateString())->required(),
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
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}