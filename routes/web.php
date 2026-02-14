<?php

use App\Http\Controllers\OnlyOfficeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// OnlyOffice routes (dengan auth middleware)
Route::middleware(['auth'])->prefix('onlyoffice')->name('onlyoffice.')->group(function () {
    Route::get('/', [OnlyOfficeController::class, 'index'])->name('index');
    Route::post('/upload', [OnlyOfficeController::class, 'upload'])->name('upload');
    Route::get('/{document}/editor', [OnlyOfficeController::class, 'editor'])->name('editor');
    Route::get('/{document}/download', [OnlyOfficeController::class, 'download'])->name('download');
    Route::post('/{document}/callback', [OnlyOfficeController::class, 'callback'])->name('callback');
    Route::delete('/{document}', [OnlyOfficeController::class, 'destroy'])->name('destroy');
});

Route::get('clearcache', function () {
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    Illuminate\Support\Facades\Artisan::call('route:clear');
    Illuminate\Support\Facades\Artisan::call('view:clear');
    Illuminate\Support\Facades\Artisan::call('config:clear');
    Illuminate\Support\Facades\Artisan::call('config:cache');
});

require __DIR__ . '/auth.php';
