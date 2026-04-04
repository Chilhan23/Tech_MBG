<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Export absensi ke PDF dengan deteksi jurusan otomatis.
     */
    public function exportPdf(Request $request)
    {
        $tingkat   = $request->query('tingkat', '');
        $kelasNama = $request->query('kelas', ''); // String nama kelas dari filter
        $jurusan   = $request->query('jurusan', '');
        $tanggal   = $request->query('tanggal', now()->toDateString());

        // --- 1. Query Data Siswa (Eager Loading Relasi Kelas) ---
        $studentsQuery = Student::with('kelas');

        // Filter: Kelas Spesifik (lewat tabel relasi 'kelas')
        if ($kelasNama) {
            $studentsQuery->whereHas('kelas', function ($q) use ($kelasNama) {
                $q->where('nama_kelas', $kelasNama);
            });
        } 
        // Filter: Tingkat (misal '10', '11', '12')
        elseif ($tingkat) {
            $studentsQuery->whereHas('kelas', function ($q) use ($tingkat) {
                $q->where('nama_kelas', 'like', $tingkat . ' %');
            });
        }

        // Filter: Jurusan (berdasarkan kolom jurusan di tabel students)
        if ($jurusan) {
            $studentsQuery->where('jurusan', $jurusan);
        }

        // Ambil data & urutkan berdasarkan Nama Kelas (relasi) lalu Nama Siswa
        $students = $studentsQuery->get()->sortBy(function($student) {
            return ($student->kelas->nama_kelas ?? '') . $student->name;
        });

        // --- 2. Ambil Data Absensi pada Tanggal Terpilih ---
        $absensiMap = Absensi::whereIn('student_id', $students->pluck('id'))
            ->whereDate('created_at', $tanggal)
            ->get()
            ->keyBy('student_id');

        // --- 3. Mapping Data untuk Baris Tabel PDF ---
        $rows = $students->map(function ($student) use ($absensiMap) {
            $absensi = $absensiMap->get($student->id);
            return [
                'name'        => $student->name,
                'nisn'        => $student->nisn,
                'kelas'       => $student->kelas ? $student->kelas->nama_kelas : '-', 
                'jurusan'     => $student->jurusan,
                'waktu'       => $absensi ? $absensi->created_at->format('H:i') : null,
                'sudah_ambil' => $absensi !== null,
            ];
        });

        // --- 4. Logika Penentuan Label Jurusan Otomatis (Mapping) ---
        $labelJurusan = 'Semua Jurusan';

        if ($jurusan) {
            // Jika user pilih filter jurusan secara manual di UI
            $labelJurusan = $jurusan;
        } elseif ($kelasNama) {
            // Deteksi kata di tengah nama kelas (Case Insensitive)
            $search = strtoupper($kelasNama);

            if (str_contains($search, 'RPL') || str_contains($search, 'PPLG')) {
                $labelJurusan = 'Rekayasa Perangkat Lunak';
            } elseif (str_contains($search, 'TJKT') || str_contains($search, 'TKJ')) {
                $labelJurusan = 'Teknik Jaringan Komputer & Telekomunikasi';
            } elseif (str_contains($search, 'TJA')) {
                $labelJurusan = 'Teknik Jaringan Akses';
            } elseif (str_contains($search, 'BP') || str_contains($search, 'PF')) {
                $labelJurusan = 'Produksi Film / Perfilman';
            }
        } elseif ($tingkat) {
            // Jika pilih tingkat (misal '10'), otomatis dianggap semua jurusan di angkatan itu
            $labelJurusan = 'Semua Jurusan (Kelas ' . $tingkat . ')';
        }

        // --- 5. Pengaturan Label Header & Nama File ---
        $labelScope   = $kelasNama ? 'Kelas ' . $kelasNama : ($tingkat ? 'Kelas ' . $tingkat : 'Semua Kelas');
        $labelTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');

        $pdf = Pdf::loadView('exports.absensi_pdf', compact(
            'rows', 'labelScope', 'labelJurusan', 'labelTanggal'
        ))->setPaper('a4', 'portrait');

        $slug = $kelasNama ? str_replace(' ', '-', strtolower($kelasNama)) : ($tingkat ? 'kelas' . $tingkat : 'semua');
        $filename = "absensi-mbg-{$tanggal}-{$slug}.pdf";

        return $pdf->download($filename);
    }
}