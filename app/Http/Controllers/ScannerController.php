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
    // ── Helper: ambil & validasi KelasLog untuk siswa ──
    private function resolveStudentAndLog(string $nisn): array
    {
        $student = Student::with('kelas')->where('nisn', $nisn)->first();

        if (!$student) {
            return [null, null, response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa tidak ditemukan.',
                'student' => null,
            ], 404)];
        }

        $kelasLog = KelasLog::where('kelas_id', $student->kelas->id)
            ->whereDate('tanggal', today())
            ->first();

        if (!$kelasLog || !$kelasLog->diambil) {
            return [null, null, response()->json([
                'success' => false,
                'message' => 'MBG kelas belum diambil dari pusat.',
                'student' => null,
            ], 422)];
        }

        if ($kelasLog->dikembalikan) {
            return [null, null, response()->json([
                'success' => false,
                'message' => 'Gagal! MBG kelas sudah dikembalikan ke pusat.',
                'student' => null,
            ], 422)];
        }

        return [$student, $kelasLog, null];
    }

    // ── Helper: format data siswa untuk response ──
    private function studentPayload(Student $student, array $extra = []): array
    {
        return array_merge([
            'name'    => $student->name,
            'nisn'    => $student->nisn,
            'kelas'   => $student->kelas?->nama_kelas,
            'jurusan' => $student->jurusan,
        ], $extra);
    }

    // Siswa Ambil MBG
    public function apiStore(Request $request)
    {
        $data = $request->validate(['nisn' => ['required', 'string']]);

        [$student, $kelasLog, $error] = $this->resolveStudentAndLog($data['nisn']);
        if ($error) return $error;

        $absensiHariIni = $student->absensis()
            ->whereDate('waktu_ambil', today())
            ->first();

        if ($absensiHariIni) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah mengambil makan hari ini.',
                'student' => $this->studentPayload($student, [
                    'waktu' => $absensiHariIni->waktu_ambil->format('H:i:s'),
                ]),
            ]);
        }

        $absensi = $student->absensis()->create([
            'waktu_ambil'  => now(),
            'kelas_log_id' => $kelasLog->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'student' => $this->studentPayload($student, [
                'waktu' => $absensi->waktu_ambil->format('H:i:s'),
            ]),
        ]);
    }

    // Siswa Kembalikan MBG
    public function apiReturn(Request $request)
    {
        $data = $request->validate(['nisn' => ['required', 'string']]);

        [$student, $kelasLog, $error] = $this->resolveStudentAndLog($data['nisn']);
        if ($error) return $error;

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
                'student' => $this->studentPayload($student, [
                    'waktu_ambil'   => $absensi->waktu_ambil->format('H:i'),
                    'waktu_kembali' => $absensi->waktu_kembali->format('H:i'),
                ]),
            ]);
        }

        $batasKembali = $absensi->waktu_ambil->addHour();
        $absensi->update(['waktu_kembali' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'MBG berhasil dikembalikan!',
            'student' => $this->studentPayload($student, [
                'waktu_ambil'   => $absensi->waktu_ambil->format('H:i'),
                'waktu_kembali' => now()->format('H:i'),
                'deadline'      => $batasKembali->format('H:i'),
            ]),
        ]);
    }

    // Scanner Kelas (Admin) - Ambil/Kembalikan MBG sekaligus untuk seluruh kelas
    public function apiKelasStore(Request $request)
    {
        $data = $request->validate(['nama_kelas' => ['required', 'string']]);

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

        // Belum ada log → catat diambil
        if (!$log) {
            $now = now();
            $kelas->update(['diambil' => $now, 'dikembalikan' => null]);
            KelasLog::create(['kelas_id' => $kelas->id, 'tanggal' => today(), 'diambil' => $now]);

            return response()->json([
                'success' => true,
                'message' => "MBG kelas {$kelas->nama_kelas} berhasil diambil!",
                'kelas'   => [
                    'nama_kelas'    => $kelas->nama_kelas,
                    'status'        => 'diambil',
                    'waktu_ambil'   => $now->format('H:i:s'),
                    'batas_kembali' => $now->copy()->addHour()->format('H:i'),
                ],
            ]);
        }

        // Sudah selesai hari ini
        if ($log->dikembalikan) {
            return response()->json([
                'success' => false,
                'message' => "MBG kelas {$kelas->nama_kelas} sudah selesai hari ini.",
                'kelas'   => [
                    'nama_kelas'    => $kelas->nama_kelas,
                    'status'        => 'selesai',
                    'waktu_ambil'   => $log->diambil->format('H:i'),
                    'waktu_kembali' => $log->dikembalikan->format('H:i'),
                ],
            ], 422);
        }

        // Sudah diambil, belum dikembalikan → proses pengembalian
        return $this->processKelasReturn($kelas, $log);
    }

    private function processKelasReturn(Kelas $kelas, KelasLog $log)
    {
        $studentIds = Student::where('kelas_id', $kelas->id)->pluck('id');

        $absensi = Absensi::whereIn('student_id', $studentIds)->whereDate('waktu_ambil', today())->count();
        $kembali = Absensi::whereIn('student_id', $studentIds)->whereDate('waktu_kembali', today())->count();

        if ($absensi !== $kembali) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada siswa yang belum mengembalikan / Scan Kembalikan MBG!',
                'kelas'   => [
                    'nama_kelas'    => $kelas->nama_kelas,
                    'status'        => 'belum semua kembali',
                    'absensi'       => $absensi,
                    'kembali'       => $kembali,
                    'belum_kembali' => $absensi - $kembali,
                ],
            ], 422);
        }

        $now          = now();
        $menitDiambil = (int) $log->diambil->diffInMinutes($now);
        $batasKembali = $log->diambil->copy()->addHour();

        if ($menitDiambil < 5) {
            return response()->json([
                'success' => false,
                'message' => "MBG kelas {$kelas->nama_kelas} baru diambil {$menitDiambil} menit yang lalu. Aktivitas Abnormal: terlalu cepat mengembalikan MBG!",
                'kelas'   => [
                    'nama_kelas'         => $kelas->nama_kelas,
                    'status'             => 'terlalu_cepat',
                    'waktu_ambil'        => $log->diambil->format('H:i'),
                    'diambil_sejak'      => "{$menitDiambil} menit lalu",
                    'bisa_kembali_mulai' => $log->diambil->copy()->addMinutes(5)->format('H:i'),
                ],
            ], 422);
        }

        if ($now->isAfter($batasKembali)) {
            $terlambat = $batasKembali->diffInMinutes($now);
            return response()->json([
                'success' => false,
                'message' => "Pengembalian MBG kelas {$kelas->nama_kelas} terlambat {$terlambat} menit! (Batas: {$batasKembali->format('H:i')})",
                'kelas'   => [
                    'nama_kelas'    => $kelas->nama_kelas,
                    'status'        => 'terlambat',
                    'waktu_ambil'   => $log->diambil->format('H:i'),
                    'batas_kembali' => $batasKembali->format('H:i'),
                    'terlambat'     => "{$terlambat} menit",
                ],
            ], 422);
        }

        $kelas->update(['dikembalikan' => $now]);
        $log->update(['dikembalikan' => $now]);

        return response()->json([
            'success' => true,
            'message' => "MBG kelas {$kelas->nama_kelas} berhasil dikembalikan!",
            'kelas'   => [
                'nama_kelas'    => $kelas->nama_kelas,
                'status'        => 'dikembalikan',
                'waktu_ambil'   => $log->diambil->format('H:i'),
                'waktu_kembali' => $now->format('H:i:s'),
                'batas_kembali' => $batasKembali->format('H:i'),
            ],
        ]);
    }

    // ── STATS ──
    public function stats(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user && $user->role === 'admin' && $user->kelas_id;

        $query = Student::query()->when($isAdmin, fn($q) => $q->where('kelas_id', $user->kelas_id));

        $total = $query->count();
        $hadir = $query->whereHas('absensis', fn($q) => $q->whereDate('waktu_ambil', today()))->count();

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