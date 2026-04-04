

<?php

use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AbsensiController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/panitia');

Route::middleware([FilamentAuthenticate::class])->group(function () {
    Route::post('/scanner/api', [ScannerController::class, 'apiStore'])->name('scanner.api');
    Route::get('/scanner/stats', [ScannerController::class, 'stats'])->name('scanner.stats');
    Route::get('/cetak-qr/bulk', [ScannerController::class, 'bulkPrint'])->name('students.qr.bulk-print');
    Route::get('/cetak-qr/{student}', [ScannerController::class, 'print'])->name('students.qr.print');
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');
    Route::get('/kelas/{kelas}/qr-print', function (App\Models\Kelas $kelas) {
        return view('exports.kelas_qr', ['kelas' => $kelas]);
    })->name('kelas.qr.print');
}); 