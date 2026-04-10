<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;


use App\Http\Controllers\Api\PatientAuthController;

Route::post('/analyze-report', [DashboardController::class, 'analyzeReport'])->name("analyze-report");
Route::post('/analyze-report-upload', [DashboardController::class, 'uploadReport'])->name("analyze-report-upload");

/**
 * Patient Auth Routes
 */
Route::prefix('patient')->group(function () {
    Route::post('login', [PatientAuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [PatientAuthController::class, 'profile']);
        Route::post('logout', [PatientAuthController::class, 'logout']);
    });
});