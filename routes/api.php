<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnlyOfficeController;

// OnlyOffice API routes (no CSRF, no auth)
Route::prefix('onlyoffice')->name('onlyoffice.')->group(function () {
    Route::get('/{document}/download', [OnlyOfficeController::class, 'download'])->name('download');
    Route::post('/{document}/callback', [OnlyOfficeController::class, 'callback'])->name('callback');
});
