<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Kelas; // Import model Kelas
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public $duplicates = [];
    public $rejected   = [];

    public function collection(Collection $rows)
    {
        $user         = Auth::user();
        $isAdminKelas = $user && $user->role === 'admin' && $user->kelas;

        foreach ($rows->skip(1) as $index => $row) {
            $rowNum   = $index + 2;
            $nisn     = trim($row[0] ?? '');
            $namaKelasExcel = trim($row[3] ?? ''); // Ambil nama kelas dari excel

            if (empty($nisn)) continue;

            // 1. CARI ID KELAS BERDASARKAN NAMA
            $kelas = Kelas::where('nama_kelas', $namaKelasExcel)->first();

            // Cek jika kelas tidak ditemukan di database
            if (!$kelas) {
                $this->rejected[] = "Baris {$rowNum} - Kelas '{$namaKelasExcel}' tidak ditemukan di database.";
                continue;
            }

            // 2. VALIDASI AKSES ADMIN KELAS
            // Bandingkan ID atau Nama (asumsi $user->kelas berisi nama kelas)
            if ($isAdminKelas && $namaKelasExcel !== $user->kelas) {
                $this->rejected[] = "Baris {$rowNum} - NISN {$nisn} ditolak (bukan kelas kamu)";
                continue;
            }

            // 3. CEK DUPLIKAT NISN
            if (Student::where('nisn', $nisn)->exists()) {
                $this->duplicates[] = "Baris {$rowNum} - NISN {$nisn} sudah ada";
                continue;
            }

            // 4. SIMPAN DENGAN KELAS_ID
            Student::create([
                'nisn'          => $nisn,
                'name'          => $row[1] ?? '',
                'jurusan'       => $row[2] ?? '',
                'kelas_id'      => $kelas->id, // Masukkan ID hasil pencarian tadi
                'jenis_kelamin' => $row[4] ?? '',
            ]);
        }
    }
}