<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            // mapping jurusan
            $jurusanMap = [
                'RPL' => 'Rekayasa Perangkat Lunak',
                'TKJ' => 'Teknik Komputer dan Jaringan',
                'TJA' => 'Tehnik Jaringan Akses',
                'PF'  => 'Perfilman',
            ];

            Student::create([
                'nisn' => $row[0],
                'name' => $row[1],
                'jurusan' => $jurusanMap[$row[2]] ?? $row[2],
                'kelas' => trim($row[3]),
                'jenis_kelamin' => $row[4],
            ]);
        }
    }
}