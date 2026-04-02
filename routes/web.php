<?php

use App\Http\Controllers\ScannerController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

Route::redirect('/', '/panitia');

Route::get('/scanner/scan', [ScannerController::class, 'scan'])->name('scanner.public_scan');

Route::middleware([FilamentAuthenticate::class])->group(function () {
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner', [ScannerController::class, 'store'])->name('scanner.store');
    Route::post('/scanner/api', [ScannerController::class, 'apiStore'])->name('scanner.api');
    Route::get('/cetak-qr/bulk', [ScannerController::class, 'bulkPrint'])->name('students.qr.bulk-print'); 
    Route::get('/cetak-qr/{student}', [ScannerController::class, 'print'])->name('students.qr.print');  
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');  
});