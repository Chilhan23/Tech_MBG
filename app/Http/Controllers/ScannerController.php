<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index(){
        return view('scanner');
    }

    public function store(Request $request){
        $data = $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $student = Student::where('nisn', $data['nisn'])->first();

        if (! $student) {
            return back()->with('error', 'QR Code tidak valid atau siswa tidak ditemukan.');
        }

        $already = $student->absensis()
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($already) {
            return back()->with('error', 'Siswa sudah mengambil makan hari ini.');
        }

        $student->absensis()->save(new Absensi());
        return back()->with('success', 'Absensi berhasil dicatat untuk ' . $student->name . '.');
    }




    public function scan(Request $request){
        $nisn = $request->query('nisn');

        if (! filled($nisn)) {
            return view('scanner_result', [
                'success' => false,
                'message' => 'Parameter NISN tidak ditemukan di URL.',
            ]);
        }

        $student = Student::where('nisn', $nisn)->first();

        if (! $student) {
            return view('scanner_result', [
                'success' => false,
                'message' => 'Siswa tidak ditemukan untuk NISN: ' . $nisn,
            ]);
        }

        $already = $student->absensis()
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($already) {
            return view('scanner_result', [
                'success' => false,
                'message' => 'Siswa sudah mengambil makan hari ini.',
            ]);
        }

        $student->absensis()->save(new Absensi());

        return view('scanner_result', [
            'success' => true,
            'message' => 'Absensi berhasil dicatat untuk ' . $student->name . '.',
        ]);
    }

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

        $already = $student->absensis()
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah mengambil makan hari ini.',
                'student' => [
                    'name'          => $student->name,
                    'nisn'          => $student->nisn,
                    'kelas'         => $student->kelas,
                    'jurusan'       => $student->jurusan,
                    'jenis_kelamin' => $student->jenis_kelamin,
                ],
            ], 200);
        }

        $student->absensis()->save(new Absensi());

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'student' => [
                'name'          => $student->name,
                'nisn'          => $student->nisn,
                'kelas'         => $student->kelas,
                'jurusan'       => $student->jurusan,
                'jenis_kelamin' => $student->jenis_kelamin,
            ],
        ], 200);
    }

    public function print(Student $student){
        return view('print_qr', compact('student'));
    }

    
    public function bulkPrint(Request $request){
        $ids = array_filter(explode(',', $request->query('ids', '')));
        abort_if(empty($ids), 400, 'Tidak ada siswa yang dipilih.');
        $students = Student::whereIn('id', $ids)->orderBy('kelas')->orderBy('name')->get();
        abort_if($students->isEmpty(), 404, 'Siswa tidak ditemukan.');
        return view('print_qr_bulk', compact('students'));
    }

    
}