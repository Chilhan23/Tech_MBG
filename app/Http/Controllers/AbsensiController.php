<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    /**
     * Export absensi ke PDF.
     *
     * Query params:
     *   tingkat  → '10' / '11' / '12'  (ambil semua kelas angkatan itu)
     *   kelas    → '11 RPL 1' dll       (satu kelas spesifik)
     *   jurusan  → nama jurusan          (opsional, bisa dikombinasi)
     *   tanggal  → Y-m-d                 (wajib)
     *
     * Prioritas: kalau kelas diisi → pakai kelas, abaikan tingkat.
     *            kalau tingkat diisi → pakai tingkat.
     *            kalau keduanya kosong → tampilkan semua siswa.
     */
    public function exportPdf(Request $request)
    {
        $tingkat = $request->query('tingkat', '');
        $kelas   = $request->query('kelas', '');
        $jurusan = $request->query('jurusan', '');
        $tanggal = $request->query('tanggal', now()->toDateString());

        // ── Query siswa sesuai filter ─────────────────────────────────
        $studentsQuery = Student::query()
            ->when($kelas,
                // Kelas spesifik diprioritaskan
                fn ($q) => $q->where('kelas', $kelas)
            )
            ->when(! $kelas && $tingkat,
                // Kalau kelas kosong, pakai tingkat (like "11 %")
                fn ($q) => $q->where('kelas', 'like', $tingkat . ' %')
            )
            ->when($jurusan,
                fn ($q) => $q->where('jurusan', $jurusan)
            )
            ->orderBy('kelas')
            ->orderBy('name');

        $students = $studentsQuery->get();

        // ── Ambil absensi pada tanggal yang dipilih ───────────────────
        $absensiMap = Absensi::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereDate('created_at', $tanggal)
            ->get()
            ->keyBy('student_id');

        // ── Gabungkan data siswa + status absensi ─────────────────────
        $rows = $students->map(function ($student) use ($absensiMap) {
            $absensi = $absensiMap->get($student->id);
            return [
                'name'        => $student->name,
                'nisn'        => $student->nisn,
                'kelas'       => $student->kelas,
                'jurusan'     => $student->jurusan,
                'waktu'       => $absensi ? $absensi->created_at->format('H:i') : null,
                'sudah_ambil' => $absensi !== null,
            ];
        });

        // ── Label untuk judul PDF ─────────────────────────────────────
        if ($kelas) {
            $labelScope = 'Kelas ' . $kelas;
        } elseif ($tingkat) {
            $labelScope = 'Kelas ' . $tingkat . ' (Semua)';
        } else {
            $labelScope = 'Semua Kelas';
        }

        $labelJurusan = $jurusan ?: 'Semua Jurusan';
        $labelTanggal = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');

        $pdf = Pdf::loadView('exports.absensi_pdf', compact(
            'rows', 'labelScope', 'labelJurusan', 'labelTanggal'
        ))->setPaper('a4', 'portrait');

        $slug     = $kelas ? str_replace(' ', '-', strtolower($kelas)) : ($tingkat ? 'kelas' . $tingkat : 'semua');
        $filename = 'absensi-mbg-' . $tanggal . '-' . $slug . '.pdf';

        return $pdf->download($filename);
    }
}