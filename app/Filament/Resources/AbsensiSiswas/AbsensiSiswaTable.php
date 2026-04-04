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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component as Livewire;

class AbsensiSiswaTable
{
    public static function configure(Table $table): Table
    {
        $user         = Auth::user();
        $isSuperAdmin = $user?->role === 'superadmin';
        $isAdmin      = $user?->role === 'admin' && $user->kelas_id;

        $filters = [];

        if ($isSuperAdmin) {
            $filters[] = SelectFilter::make('tingkat')
                ->label('Tingkat Kelas')
                ->options(['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12'])
                ->query(fn (Builder $q, array $data) => $q->when(
                    $data['value'] ?? null,
                    fn ($q2, $v) => $q2->whereHas('student.kelas',
                        fn ($q3) => $q3->where('nama_kelas', 'like', $v . ' %'))
                ));

            $filters[] = SelectFilter::make('jurusan')
                ->label('Jurusan')
                ->options(fn () => Student::distinct()->orderBy('jurusan')
                    ->pluck('jurusan', 'jurusan')->toArray())
                ->query(fn (Builder $q, array $data) => $q->when(
                    $data['value'] ?? null,
                    fn ($q2, $v) => $q2->whereHas('student',
                        fn ($q3) => $q3->where('jurusan', $v))
                ));

            $filters[] = SelectFilter::make('kelas')
                ->label('Kelas')
                ->options(fn () => Kelas::orderBy('nama_kelas')
                    ->pluck('nama_kelas', 'id')->toArray())
                ->query(fn (Builder $q, array $data) => $q->when(
                    $data['value'] ?? null,
                    fn ($q2, $v) => $q2->whereHas('student',
                        fn ($q3) => $q3->where('kelas_id', $v))
                ));
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
            ->filters($filters)
            ->modifyQueryUsing(function (Builder $query, Livewire $livewire) use ($isAdmin, $user) {
                if ($isAdmin) {
                    $query->whereHas('student',
                        fn ($q) => $q->where('kelas_id', $user->kelas_id));
                }
                $filterData = $livewire->tableFilters['tanggal'] ?? [];
                $adaFilter  = !empty($filterData['dari_tanggal']) || !empty($filterData['sampai_tanggal']);
                if (!$adaFilter) {
                    $query->whereDate('waktu_ambil', today());
                }
            })
            ->toolbarActions($toolbarActions)
            ->defaultSort('waktu_ambil', 'desc');
    }

    
}
