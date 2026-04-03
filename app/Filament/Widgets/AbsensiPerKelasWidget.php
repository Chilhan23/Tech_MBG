<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Student;
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
        return $record->kelas;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $query = Student::query()
            ->select('kelas')
            ->selectRaw('COUNT(*) as total_siswa')
            ->selectRaw('SUM(CASE WHEN EXISTS (
                SELECT 1 FROM absensis
                WHERE absensis.student_id = students.id
                AND DATE(absensis.created_at) = CURDATE()
            ) THEN 1 ELSE 0 END) as sudah_ambil')
            ->groupBy('kelas')
            ->orderBy('kelas');

        // Admin kelas hanya lihat kelasnya sendiri
        if ($user && $user->role === 'admin' && $user->kelas) {
            $query->where('kelas', $user->kelas);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('kelas')
                    ->label('Kelas'),
                TextColumn::make('total_siswa')
                    ->label('Total Siswa'),
                TextColumn::make('sudah_ambil')
                    ->label('Sudah Ambil MBG')
                    ->color('success'),
                TextColumn::make('belum_ambil')
                    ->label('Belum Ambil')
                    ->getStateUsing(fn ($record) => $record->total_siswa - $record->sudah_ambil)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
            ]);
    }
}