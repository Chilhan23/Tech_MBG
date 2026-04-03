<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public $duplicates = [];
    public $rejected   = [];

    public function collection(Collection $rows)
    {
        $user        = Auth::user();
        $isAdminKelas = $user && $user->role === 'admin' && $user->kelas;

        foreach ($rows->skip(1) as $index => $row) {
            $rowNum  = $index + 2;
            $nisn    = trim($row[0] ?? '');
            $kelasRow = trim($row[3] ?? '');

            // Skip baris kosong
            if (empty($nisn)) continue;

            // Validasi kelas — admin kelas hanya boleh import kelasnya sendiri
            if ($isAdminKelas && $kelasRow !== $user->kelas) {
                $this->rejected[] = "Baris {$rowNum} - NISN {$nisn} ditolak (kelas '{$kelasRow}' bukan kelas kamu)";
                continue;
            }

            // Cek duplikat NISN
            if (Student::where('nisn', $nisn)->exists()) {
                $this->duplicates[] = "Baris {$rowNum} - NISN {$nisn} sudah ada";
                continue;
            }

            Student::create([
                'nisn'          => $nisn,
                'name'          => $row[1] ?? '',
                'jurusan'       => $row[2] ?? '',
                'kelas'         => $kelasRow,
                'jenis_kelamin' => $row[4] ?? '',
            ]);
        }
    }
}