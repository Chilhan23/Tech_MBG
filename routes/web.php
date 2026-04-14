<?php

use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AbsensiController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/panitia');

Route::middleware([FilamentAuthenticate::class])->group(function () {
    // --- API Scanner Siswa ---
    //  Ambil
    Route::post('/scanner/api', [ScannerController::class, 'apiStore'])->name('scanner.api');
    // Mode Kembali 
    Route::post('/scanner/api/return', [ScannerController::class, 'apiReturn'])->name('scanner.api.return');
    
    // --- API Scanner Kelas (Pusat) ---
    // Mode Ambil & Kembali khusus Box Ompreng Kelas
    Route::post('/scanner/api/kelas', [ScannerController::class, 'apiKelasStore'])->name('scanner.api.kelas');
    Route::get('/scanner/kelas/check', [ScannerController::class, 'apiKelasCheck'])->name('scanner.api.kelas.check');

    // --- Stats & PDF ---
    Route::get('/scanner/stats', [ScannerController::class, 'stats'])->name('scanner.stats');
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');

    // --- Cetak QR ---
    Route::get('/cetak-qr/bulk', [ScannerController::class, 'bulkPrint'])->name('students.qr.bulk-print');
    Route::get('/cetak-qr/{student}', [ScannerController::class, 'print'])->name('students.qr.print');
    Route::get('/kelas/{kelas}/qr-print', function (App\Models\Kelas $kelas) {
        return view('exports.kelas_qr', ['kelas' => $kelas]);
    })->name('kelas.qr.print');
});