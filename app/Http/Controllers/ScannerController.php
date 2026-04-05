<?php
namespace App\Http\Controllers;
use App\Models\KelasLog;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    // ── AMBIL (Siswa scan untuk ambil makan) ──
    public function apiStore(Request $request)
    {
        $data = $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $student = Student::with('kelas')->where('nisn', $data['nisn'])->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa tidak ditemukan.',
                'student' => null,
            ], 404);
        }

        // Cek mbg kelas sudah diambil 
        if (!$student->kelas?->diambil) {
            return response()->json([
                'success' => false,
                'message' => 'MBG kelas belum diambil dari pusat.',
                'student' => null,
            ], 422);
        }

        // Cek mbg sudah dikembalikan 
        if ($student->kelas?->dikembalikan) {
            return response()->json([
                'success' => false,
                'message' => 'MBG kelas sudah dikembalikan ke pusat.',
                'student' => null,
            ], 422);
        }

        $absensiHariIni = $student->absensis()
            ->whereDate('waktu_ambil', now()->toDateString())
            ->first();

        if ($absensiHariIni) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah mengambil makan hari ini.',
                'student' => [
                    'name'   => $student->name,
                    'nisn'   => $student->nisn,
                    'kelas'  => $student->kelas?->nama_kelas,
                    'jurusan'=> $student->jurusan,
                    'waktu'  => $absensiHariIni->waktu_ambil->format('H:i:s'),
                ],
            ], 200);
        }

        // Catat absensi
        $absensi = $student->absensis()->create([
            'waktu_ambil' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'student' => [
                'name'   => $student->name,
                'nisn'   => $student->nisn,
                'kelas'  => $student->kelas?->nama_kelas,
                'jurusan'=> $student->jurusan,
                'waktu'  => $absensi->waktu_ambil->format('H:i:s'),
            ],
        ], 200);
    }

    // ── kembalikan (Siswa scan setelah selesai makan) ──
    public function apiReturn(Request $request)
    {
        $data = $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $student = Student::with('kelas')->where('nisn', $data['nisn'])->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan!',
                'student' => null,
            ], 404);
        }

        // Cek ompreng sudah dikembalikan ke pusat
        if ($student->kelas?->dikembalikan) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! MBG kelas sudah dikembalikan ke pusat.',
                'student' => null,
            ], 422);
        }

        $absensi = $student->absensis()
            ->whereDate('waktu_ambil', today())
            ->first();

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum melakukan scan AMBIL hari ini!',
                'student' => null,
            ], 422);
        }

        if ($absensi->waktu_kembali) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah mengembalikan MBG sebelumnya.',
                'student' => [
                    'name'          => $student->name,
                    'nisn'          => $student->nisn,
                    'kelas'         => $student->kelas?->nama_kelas,
                    'jurusan'       => $student->jurusan,
                    'waktu_ambil'   => $absensi->waktu_ambil->format('H:i'),
                    'waktu_kembali' => $absensi->waktu_kembali->format('H:i'),
                ],
            ], 200);
        }

        $batasKembali = $absensi->waktu_ambil->addHour();
        $absensi->update(['waktu_kembali' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'MBG berhasil dikembalikan!',
            'student' => [
                'name'          => $student->name,
                'nisn'          => $student->nisn,
                'kelas'         => $student->kelas?->nama_kelas,
                'jurusan'       => $student->jurusan,
                'waktu_ambil'   => $absensi->waktu_ambil->format('H:i'),
                'waktu_kembali' => now()->format('H:i'),
                'deadline'      => $batasKembali->format('H:i'),
            ],
        ], 200);
    }

    // ── KELAS (Pusat scan QR Kelas untuk ambil/kembalikan ompreng) ──
   public function apiKelasStore(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string'],
        ]);

        $kelas = Kelas::where('nama_kelas', $data['nama_kelas'])->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.',
            ], 404);
        }

        $log = KelasLog::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', today())
            ->first();

        // Belum ada log hari ini → catat diambil
        if (!$log) {
            $now = now();

            $kelas->update(['diambil' => $now, 'dikembalikan' => null]);

            KelasLog::create([
                'kelas_id' => $kelas->id,
                'tanggal'  => today(),
                'diambil'  => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'MBG kelas ' . $kelas->nama_kelas . ' berhasil diambil!',
                'kelas'   => [
                    'nama_kelas'  => $kelas->nama_kelas,
                    'status'      => 'diambil',
                    'waktu_ambil' => $now->format('H:i:s'),
                    'batas_kembali' => $now->copy()->addHour()->format('H:i'),
                ],
            ]);
        }

        // Sudah diambil tapi belum dikembalikan
        if (!$log->dikembalikan) {
            $now          = now();
            $batasKembali = $log->diambil->copy()->addHour();

            // Validasi: lewat 1 jam
            if ($now->isAfter($batasKembali)) {
                $terlambat = $batasKembali->diffInMinutes($now);

                return response()->json([
                    'success' => false,
                    'message' => 'Pengembalian MBG kelas ' . $kelas->nama_kelas
                        . ' terlambat ' . $terlambat . ' menit!'
                        . ' (Batas: ' . $batasKembali->format('H:i') . ')',
                    'kelas' => [
                        'nama_kelas'    => $kelas->nama_kelas,
                        'status'        => 'terlambat',
                        'waktu_ambil'   => $log->diambil->format('H:i'),
                        'batas_kembali' => $batasKembali->format('H:i'),
                        'terlambat'     => $terlambat . ' menit',
                    ],
                ], 422);
            }

            $kelas->update(['dikembalikan' => $now]);
            $log->update(['dikembalikan' => $now]);

            return response()->json([
                'success' => true,
                'message' => 'MBG kelas ' . $kelas->nama_kelas . ' berhasil dikembalikan!',
                'kelas'   => [
                    'nama_kelas'    => $kelas->nama_kelas,
                    'status'        => 'dikembalikan',
                    'waktu_ambil'   => $log->diambil->format('H:i'),
                    'waktu_kembali' => $now->format('H:i:s'),
                    'batas_kembali' => $batasKembali->format('H:i'),
                ],
            ]);
        }

        // Sudah selesai hari ini
        return response()->json([
            'success' => false,
            'message' => 'MBG kelas ' . $kelas->nama_kelas . ' sudah selesai hari ini.',
            'kelas'   => [
                'nama_kelas'    => $kelas->nama_kelas,
                'status'        => 'selesai',
                'waktu_ambil'   => $log->diambil->format('H:i'),
                'waktu_kembali' => $log->dikembalikan->format('H:i'),
            ],
        ], 422);
    }

    
    public function stats(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user && $user->role === 'admin' && $user->kelas_id;

        $studentsQuery = Student::query();
        if ($isAdmin) {
            $studentsQuery->where('kelas_id', $user->kelas_id);
        }

        $total = $studentsQuery->count();
        $hadir = $studentsQuery->whereHas('absensis', function ($q) {
            $q->whereDate('waktu_ambil', today());
        })->count();

        return response()->json([
            'total' => $total,
            'hadir' => $hadir,
            'belum' => $total - $hadir,
        ]);
    }

    // ── PRINT ──
    public function print(Student $student)
    {
        return view('print_qr', compact('student'));
    }

    public function bulkPrint(Request $request)
    {
        $ids = array_filter(explode(',', $request->query('ids', '')));
        abort_if(empty($ids), 400, 'Tidak ada siswa yang dipilih.');
        $students = Student::with('kelas')
            ->whereIn('id', $ids)
            ->orderBy('kelas_id')
            ->orderBy('name')
            ->get();
        abort_if($students->isEmpty(), 404, 'Siswa tidak ditemukan.');
        return view('print_qr_bulk', compact('students'));
    }
}