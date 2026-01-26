<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RppController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // RPP Management
    Route::resource('rpp', RppController::class)->except(['edit', 'update']);
    Route::get('rpp/{rpp}/pdf', [RppController::class, 'downloadPdf'])->name('rpp.pdf');
    Route::get('rpp/{rpp}/word', [RppController::class, 'downloadWord'])->name('rpp.word');

    // STS Management
    Route::get('sts', [\App\Http\Controllers\StsController::class, 'index'])->name('sts.index');
    Route::get('sts/create', [\App\Http\Controllers\StsController::class, 'create'])->name('sts.create');
    Route::post('sts', [\App\Http\Controllers\StsController::class, 'store'])->name('sts.store');
    Route::get('sts/{sts}', [\App\Http\Controllers\StsController::class, 'show'])->name('sts.show');
    Route::delete('sts/{sts}', [\App\Http\Controllers\StsController::class, 'destroy'])->name('sts.destroy');
    Route::get('sts/{sts}/word', [\App\Http\Controllers\StsController::class, 'downloadWord'])->name('sts.word');
    Route::get('sts/{sts}/pdf', [\App\Http\Controllers\StsController::class, 'downloadPdf'])->name('sts.pdf');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/logo', [SettingController::class, 'deleteLogo'])->name('settings.delete-logo');
    Route::delete('/settings/logo-kanan', [SettingController::class, 'deleteLogoKanan'])->name('settings.delete-logo-kanan');
    Route::delete('/settings/kop-surat', [SettingController::class, 'deleteKopSurat'])->name('settings.delete-kop-surat');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users-template', [UserController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users-import', [UserController::class, 'import'])->name('users.import');
    });
});

require __DIR__.'/auth.php';
