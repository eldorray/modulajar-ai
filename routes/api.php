<?php

use App\Http\Controllers\Api\V1\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Api\V1\Admin\RppController as AdminRppController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RppController;
use App\Http\Controllers\Api\V1\RppOptionsController;
use App\Http\Controllers\Api\V1\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('dashboard', [DashboardController::class, 'show']);
        Route::get('rpp-options', [RppOptionsController::class, 'show']);

        Route::get('rpps', [RppController::class, 'index']);
        Route::post('rpps', [RppController::class, 'store']);
        Route::get('rpps/{id}', [RppController::class, 'show'])->whereNumber('id');
        Route::get('rpps/{id}/status', [RppController::class, 'status'])->whereNumber('id');
        Route::delete('rpps/{id}', [RppController::class, 'destroy'])->whereNumber('id');
        Route::get('rpps/{id}/pdf', [RppController::class, 'pdf'])->whereNumber('id');
        Route::get('rpps/{id}/docx', [RppController::class, 'docx'])->whereNumber('id');

        Route::get('profile', [ProfileController::class, 'show']);
        Route::patch('profile', [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'password']);
        Route::delete('profile', [ProfileController::class, 'destroy']);

        Route::get('settings', [SettingController::class, 'show']);
        Route::patch('settings', [SettingController::class, 'update'])->middleware('capability:settings.manage');
        Route::delete('settings/{asset}', [SettingController::class, 'deleteAsset'])
            ->whereIn('asset', ['logo', 'logo_kanan', 'kop_surat'])
            ->middleware('capability:settings.manage');

        Route::prefix('admin')->group(function (): void {
            Route::apiResource('users', AdminUserController::class)->middleware('capability:admin.users.manage');
            Route::get('guru', [AdminGuruController::class, 'index'])->middleware('capability:admin.guru.manage');
            Route::get('guru/{guru}', [AdminGuruController::class, 'show'])->middleware('capability:admin.guru.manage');
            Route::patch('guru/{guru}', [AdminGuruController::class, 'update'])->middleware('capability:admin.guru.manage');
            Route::get('rpps', [AdminRppController::class, 'index'])->middleware('capability:admin.rpp.view');
            Route::get('rpps/{rpp}/pdf', [AdminRppController::class, 'pdf'])->middleware('capability:admin.rpp.view');
            Route::get('rpps/{rpp}', [AdminRppController::class, 'show'])->middleware('capability:admin.rpp.view');
        });
    });
});
