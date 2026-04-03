<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function apiStore(Request $request){
        $data = $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $student = Student::where('nisn', $data['nisn'])->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa tidak ditemukan.',
                'student' => null,
            ], 404);
        }

        $absensiHariIni = $student->absensis()
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($absensiHariIni) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah mengambil makan hari ini.',
                'student' => [
                    'name'          => $student->name,
                    'nisn'          => $student->nisn,
                    'kelas'         => $student->kelas,
                    'jurusan'       => $student->jurusan,
                    'jenis_kelamin' => $student->jenis_kelamin,
                    'waktu'         => $absensiHariIni->created_at->format('H:i:s'),
                ],
            ], 200);
        }

        // Belum ambil — catat sekarang
        $absensi = new Absensi();
        $student->absensis()->save($absensi);
        $absensi->refresh(); // pastikan created_at terisi

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'student' => [
                'name'          => $student->name,
                'nisn'          => $student->nisn,
                'kelas'         => $student->kelas,
                'jurusan'       => $student->jurusan,
                'jenis_kelamin' => $student->jenis_kelamin,
                'waktu'         => $absensi->created_at->format('H:i:s'),
            ],
        ], 200);
    }

    

    public function print(Student $student)
    {
        return view('print_qr', compact('student'));
    }

    public function bulkPrint(Request $request)
    {
        $ids = array_filter(explode(',', $request->query('ids', '')));
        abort_if(empty($ids), 400, 'Tidak ada siswa yang dipilih.');
        $students = Student::whereIn('id', $ids)->orderBy('kelas')->orderBy('name')->get();
        abort_if($students->isEmpty(), 404, 'Siswa tidak ditemukan.');
        return view('print_qr_bulk', compact('students'));
    }
}