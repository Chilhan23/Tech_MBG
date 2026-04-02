<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public $duplicates = [];
    public function collection(Collection $rows)
    {
       

        foreach ($rows->skip(1) as $index => $row) {

            if (Student::where('nisn', $row[0])->exists()) {
                $this->duplicates[] = "Baris " . ($index + 2) . " - NISN {$row[0]} sudah ada";
                continue;
            }

            Student::create([
                'nisn' => trim($row[0]),
                'name' => $row[1],
                'jurusan' => $row[2],
                'kelas' => $row[3],
                'jenis_kelamin' => $row[4],
            ]);
        }
    }
}