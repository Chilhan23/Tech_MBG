<?php

namespace App\Filament\Widgets;

use App\Models\Kelas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AbsensiPerKelasWidget extends BaseWidget
{
    protected static ?string $heading = 'Rekap Absensi Per Kelas Hari Ini';
    protected int | string | array $columnSpan = 'full';

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        // Bind start/end of day sebagai parameter agar composite index
        // (waktu_ambil, student_id) dapat dimanfaatkan — bukan DATE()/CURDATE()
        // yang membungkus kolom dan mencegah index dipakai.
        $start = today()->startOfDay()->toDateTimeString();
        $end   = today()->endOfDay()->toDateTimeString();

        $query = Kelas::query()
            ->select('kelas.id', 'kelas.nama_kelas')
            ->withCount('students as total_siswa')
            ->selectRaw('(
                SELECT COUNT(DISTINCT absensis.student_id)
                FROM absensis
                JOIN students ON absensis.student_id = students.id
                WHERE students.kelas_id = kelas.id
                AND absensis.waktu_ambil BETWEEN ? AND ?
            ) as sudah_ambil', [$start, $end])
            ->orderBy('nama_kelas');

        // Admin kelas hanya lihat kelasnya sendiri
        if ($user && $user->role === 'admin' && $user->kelas) {
            $query->where('id', $user->kelas_id);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('nama_kelas')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('total_siswa')
                    ->label('Total Siswa')
                    ->alignCenter(),

                TextColumn::make('sudah_ambil')
                    ->label('Sudah Ambil MBG')
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('belum_ambil')
                    ->label('Belum Ambil')
                    ->getStateUsing(fn ($record) => $record->total_siswa - $record->sudah_ambil)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->alignCenter(),
            ]);
    }
}