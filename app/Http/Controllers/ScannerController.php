<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('scanner');
    }

    public function store(Request $request)
    {
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

    public function print(Student $student)
    {
        return view('print_qr', compact('student'));
    }
}
