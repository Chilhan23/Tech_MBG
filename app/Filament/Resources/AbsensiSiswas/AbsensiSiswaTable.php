<?php

namespace App\Filament\Resources\AbsensiSiswas;

use App\Models\Kelas;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaTable
{
    public static function configure(Table $table): Table
    {
        $user         = Auth::user();
        $isSuperAdmin = $user?->role === 'superadmin';
        $isAdmin      = $user?->role === 'admin' && $user->kelas_id;

        $filters = [];

        if ($isSuperAdmin) {
            $filters[] = Filter::make('tingkat')
                ->label('Tingkat Kelas')
                ->form([
                    Select::make('tingkat')
                        ->label('Tingkat')
                        ->placeholder('Semua')
                        ->options(['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12']),
                ])
                ->query(function (Builder $q, array $data) {
                    if (!empty($data['tingkat'])) {
                        $studentIds = Student::whereHas('kelas', fn ($k) => 
                            $k->where('nama_kelas', 'like', $data['tingkat'] . ' %'))->pluck('id');
                        $q->whereIn('student_id', $studentIds);
                    }
                    return $q;
                });

            $filters[] = Filter::make('jurusan')
                ->label('Jurusan')
                ->form([
                    Select::make('jurusan')
                        ->label('Jurusan')
                        ->placeholder('Semua')
                        ->options([
                            'Rekayasa Perangkat Lunak' => 'Rekayasa Perangkat Lunak',
                            'Teknik Komputer dan Jaringan' => 'Teknik Komputer dan Jaringan',
                            'Teknik Jaringan Akses' => 'Teknik Jaringan Akses',
                            'Perfilman' => 'Perfilman',
                        ]),
                ])
                ->query(function (Builder $q, array $data) {
                    if (!empty($data['jurusan'])) {
                        $studentIds = Student::where('jurusan', $data['jurusan'])->pluck('id');
                        $q->whereIn('student_id', $studentIds);
                    }
                    return $q;
                });

            $filters[] = Filter::make('kelas_filter')
                ->label('Kelas')
                ->form([
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->placeholder('Semua')
                        ->options(fn () => Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray()),
                ])
                ->query(function (Builder $q, array $data) {
                    if (!empty($data['kelas_id'])) {
                        $studentIds = Student::where('kelas_id', $data['kelas_id'])->pluck('id');
                        $q->whereIn('student_id', $studentIds);
                    }
                    return $q;
                });
        }

        $filters[] = Filter::make('tanggal')
            ->label('Tanggal')
            ->form([
                DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
            ])
            ->query(fn (Builder $q, array $data) => $q
                ->when($data['dari_tanggal'] ?? null,
                    fn ($q2, $d) => $q2->whereDate('waktu_ambil', '>=', $d))
                ->when($data['sampai_tanggal'] ?? null,
                    fn ($q2, $d) => $q2->whereDate('waktu_ambil', '<=', $d))
            );

        $toolbarActions = [];

        if ($isSuperAdmin) {
            $toolbarActions[] = Action::make('export_pdf')
                ->label('Export PDF')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('danger')
                ->form([
                    Select::make('tingkat')->label('Tingkat')
                        ->placeholder('— Semua —')
                        ->options(['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12'])
                        ->live()->afterStateUpdated(fn ($set) => $set('kelas', null)),
                    Select::make('kelas')->label('Kelas Spesifik')
                        ->placeholder('— Semua —')
                        ->options(fn () => Kelas::orderBy('nama_kelas')
                            ->pluck('nama_kelas', 'nama_kelas')->toArray())
                        ->live()->afterStateUpdated(fn ($set) => $set('tingkat', null)),
                    Select::make('jurusan')->label('Jurusan')
                        ->placeholder('Semua Jurusan')
                        ->options(fn () => Student::distinct()->orderBy('jurusan')
                            ->pluck('jurusan', 'jurusan')->toArray()),
                    DatePicker::make('tanggal')->label('Tanggal')
                        ->default(now()->toDateString())->required(),
                ])
                ->action(function (array $data) {
                    $params = http_build_query(array_filter([
                        'tingkat' => $data['tingkat'] ?? '',
                        'kelas'   => $data['kelas']   ?? '',
                        'jurusan' => $data['jurusan'] ?? '',
                        'tanggal' => $data['tanggal'] ?? today()->toDateString(),
                    ]));
                    redirect()->away(route('absensi.export-pdf') . '?' . $params);
                });
        }

        if ($isAdmin) {
            $toolbarActions[] = Action::make('export_pdf_admin')
                ->label('Export PDF Kelas')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default(now()->toDateString())
                        ->required(),
                ])
                ->action(function (array $data) use ($user) {
                    $namaKelas = Kelas::find($user->kelas_id)?->nama_kelas;
                    $params = http_build_query([
                        'kelas'   => $namaKelas,
                        'tanggal' => $data['tanggal'],
                    ]);
                    return redirect()->away(route('absensi.export-pdf') . '?' . $params);
                });
        }

        $toolbarActions[] = BulkActionGroup::make([
            DeleteBulkAction::make(),
        ]);

        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')->searchable()->sortable(),
                TextColumn::make('student.nisn')
                    ->label('NISN')->sortable(),
                TextColumn::make('student.kelas.nama_kelas')
                    ->label('Kelas')->sortable(),
                TextColumn::make('student.jurusan')
                    ->label('Jurusan')->sortable()->visible($isSuperAdmin),
                TextColumn::make('waktu_ambil')
                    ->label('Waktu Ambil')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('waktu_kembali')
                    ->label('Waktu Kembali')->dateTime('d/m/Y H:i')
                    ->placeholder('Belum kembali')
                    ->sortable()->visible($isAdmin || $isSuperAdmin),
            ])
            // ->filters($filters, layout: FiltersLayout::AboveContent)
            // ->filtersFormColumns(2)
            // ->deferFilters(false)
            // ->filtersApplyAction(fn () => null)
            ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $user) {
                if ($isAdmin) {
                    // Filter ke kelas user saja (admin hanya lihat data kelasnya)
                    $query->whereIn('student_id',
                        Student::where('kelas_id', $user->kelas_id)->pluck('id'));
                }
            })
            ->toolbarActions($toolbarActions)
            ->defaultSort('waktu_ambil', 'desc');
    }

    
}
