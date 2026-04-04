<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function exportPdf(Request $request)
    {
        $tingkat   = $request->query('tingkat', '');
        $kelasNama = $request->query('kelas', '');
        $jurusan   = $request->query('jurusan', '');
        $tanggal   = $request->query('tanggal', now()->toDateString());

        // --- 1. Query Students ---
        $studentsQuery = Student::with('kelas');

        if ($kelasNama) {
            $studentsQuery->whereHas('kelas', function ($q) use ($kelasNama) {
                $q->where('nama_kelas', $kelasNama);
            });
        } elseif ($tingkat) {
            $studentsQuery->whereHas('kelas', function ($q) use ($tingkat) {
                $q->where('nama_kelas', 'like', $tingkat . ' %');
            });
        }

        if ($jurusan) {
            $studentsQuery->where('jurusan', $jurusan);
        }

        $students = $studentsQuery->get()->sortBy(function ($student) {
            return (optional($student->kelas)->nama_kelas ?? '') . $student->name;
        });

        // --- 2. Query Absensi (FIX DISINI) ---
        $absensiMap = Absensi::whereIn('student_id', $students->pluck('id'))
            ->whereDate('waktu_ambil', $tanggal) // ✅ FIX
            ->get()
            ->keyBy('student_id');

        // --- 3. Mapping ---
        $rows = $students->map(function ($student) use ($absensiMap) {
            $absensi = $absensiMap->get($student->id);

            return [
                'name'        => $student->name,
                'nisn'        => $student->nisn,
                'kelas'       => optional($student->kelas)->nama_kelas ?? '-',
                'jurusan'     => $student->jurusan,
                'waktu_ambil'   => $absensi ? $absensi->waktu_ambil?->format('H:i') : null,
                'waktu_kembali' => $absensi && $absensi->waktu_kembali
                    ? $absensi->waktu_kembali->format('H:i')
                    : null,
                'sudah_ambil' => $absensi !== null,
            ];
        });

        // --- 4. Label Jurusan ---
        $labelJurusan = 'Semua Jurusan';

        if ($jurusan) {
            $labelJurusan = $jurusan;
        } elseif ($kelasNama) {
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
            $labelJurusan = 'Semua Jurusan (Kelas ' . $tingkat . ')';
        }

        // --- 5. Header ---
        $labelScope   = $kelasNama
            ? 'Kelas ' . $kelasNama
            : ($tingkat ? 'Kelas ' . $tingkat : 'Semua Kelas');

        $labelTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');

        // --- 6. Generate PDF ---
        $pdf = Pdf::loadView('exports.absensi_pdf', compact(
            'rows',
            'labelScope',
            'labelJurusan',
            'labelTanggal'
        ))->setPaper('a4', 'portrait');

        $slug = $kelasNama
            ? str_replace(' ', '-', strtolower($kelasNama))
            : ($tingkat ? 'kelas' . $tingkat : 'semua');

        $filename = "absensi-mbg-{$tanggal}-{$slug}.pdf";

        return $pdf->download($filename);
    }
}