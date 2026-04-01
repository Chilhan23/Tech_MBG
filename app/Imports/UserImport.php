<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public function collection(Collection $rows)
    {
       $duplicates = [];

        foreach ($rows->skip(1) as $index => $row) {

            if (Student::where('nisn', $row[0])->exists()) {
                $duplicates[] = "Baris " . ($index + 1) . " - NISN {$row[0]} sudah ada";
                continue;
            }

            Student::create([
                'nisn' => $row[0],
                'name' => $row[1],
                'jurusan' => $row[2],
                'kelas' => $row[3],
                'jenis_kelamin' => $row[4],
            ]);
        }
    }
}