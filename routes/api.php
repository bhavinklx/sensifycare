<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;


use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\PatientSymptomController;

Route::post('/analyze-report', [DashboardController::class, 'analyzeReport'])->name("analyze-report");
Route::post('/analyze-report-upload', [DashboardController::class, 'uploadReport'])->name("analyze-report-upload");

/**
 * Patient Auth Routes
 */
Route::post('patient-login', [PatientAuthController::class, 'login']);
Route::post('patient-signup', [PatientAuthController::class, 'signup']);
Route::post('patient-send-otp', [PatientAuthController::class, 'sendOtp']);
Route::post('patient-verify-otp', [PatientAuthController::class, 'verifyOtp']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('patient-profile', [PatientAuthController::class, 'profile']);
    Route::post('patient-logout', [PatientAuthController::class, 'logout']);
    
    // Symptoms API
    Route::get('patient-symptoms', [PatientSymptomController::class, 'index']);
    Route::post('patient-symptoms', [PatientSymptomController::class, 'save']);
});