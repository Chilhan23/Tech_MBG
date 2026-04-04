<?php

namespace App\Filament\Widgets;

use App\Models\Kelas;
use App\Models\Student;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Kita mulai query dari Model Kelas
        $query = Kelas::query()
            ->select('kelas.id', 'kelas.nama_kelas')
            // Hitung total siswa di kelas tersebut
            ->withCount('students as total_siswa')
            // Hitung siswa yang SUDAH absensi hari ini menggunakan subquery
            ->selectRaw('(
                SELECT COUNT(*) 
                FROM absensis 
                JOIN students ON absensis.student_id = students.id 
                WHERE students.kelas_id = kelas.id 
                AND DATE(absensis.waktu_ambil) = CURDATE()
            ) as sudah_ambil')
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
                    // Hitung selisih: Total Siswa - Sudah Ambil
                    ->getStateUsing(fn ($record) => $record->total_siswa - $record->sudah_ambil)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->alignCenter(),
            ]);
    }
}