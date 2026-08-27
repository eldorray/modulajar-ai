<?php

use App\Http\Controllers\Admin\AiSettingController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pwa\GuruAppController;
use App\Http\Controllers\RppController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman offline PWA (harus bisa diakses tanpa sesi/koneksi)
Route::get('app/offline', [GuruAppController::class, 'offline'])->name('pwa.offline');

Route::middleware(['auth', 'verified'])->group(function () {
    // PWA Guru (mobile app)
    Route::prefix('app')->name('pwa.')->group(function () {
        Route::get('/', [GuruAppController::class, 'home'])->name('home');
        Route::get('rpp', [GuruAppController::class, 'index'])->name('rpp.index');
        Route::get('rpp/create', [GuruAppController::class, 'create'])->name('rpp.create');
        Route::get('rpp/{rpp}', [GuruAppController::class, 'show'])->name('rpp.show');
        Route::get('akun', [GuruAppController::class, 'akun'])->name('akun');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // RPP Management
    Route::resource('rpp', RppController::class)->except(['edit', 'update']);
    Route::get('rpp/{rpp}/pdf', [RppController::class, 'downloadPdf'])->name('rpp.pdf');
    Route::get('rpp/{rpp}/word', [RppController::class, 'downloadWord'])->name('rpp.word');
    Route::get('rpp/{rpp}/print', [RppController::class, 'print'])->name('rpp.print');

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

        // Guru Management
        Route::resource('guru', GuruController::class)->except(['create', 'store', 'destroy']);
        Route::post('guru/sync', [GuruController::class, 'sync'])->name('guru.sync');

        // RPP dari Guru
        Route::get('rpp', [\App\Http\Controllers\Admin\RppController::class, 'index'])->name('rpp.index');

        // Pengaturan AI
        Route::get('ai', [AiSettingController::class, 'edit'])->name('ai.edit');
        Route::put('ai', [AiSettingController::class, 'update'])->name('ai.update');
        Route::delete('ai/api-key', [AiSettingController::class, 'destroyApiKey'])->name('ai.delete-key');
        Route::post('ai/test', [AiSettingController::class, 'test'])->name('ai.test');
    });
});

require __DIR__.'/auth.php';
