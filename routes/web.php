<?php

use App\Http\Controllers\ScannerController;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/panitia');

Route::get('/scanner/scan', [ScannerController::class, 'scan'])->name('scanner.public_scan');

Route::middleware([FilamentAuthenticate::class])->group(function () {
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner', [ScannerController::class, 'store'])->name('scanner.store');
    Route::get('/cetak-qr/{student}', [ScannerController::class, 'print'])->name('students.qr.print');
});