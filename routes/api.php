<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;


use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\PatientSymptomController;
use App\Http\Controllers\Api\PatientHealthParameterController;

Route::post('/analyze-report', [DashboardController::class, 'analyzeReport'])->name("analyze-report");
Route::post('/analyze-report-upload', [DashboardController::class, 'uploadReport'])->name("analyze-report-upload");

/**
 * Patient Auth Routes
 */

Route::post('patient-signup', [PatientAuthController::class, 'signup']);
Route::post('patient-send-otp', [PatientAuthController::class, 'sendOtp']);
Route::post('patient-verify-otp', [PatientAuthController::class, 'verifyOtp']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('patient-logout', [PatientAuthController::class, 'logout']);
    
    // Profile API
    Route::get('patient-profile', [PatientAuthController::class, 'getProfile']);
    Route::post('patient-profile/update', [PatientAuthController::class, 'updateProfile']);
    
    // Symptoms API
    Route::get('patient-symptoms', [PatientSymptomController::class, 'index']);
    Route::get('patient-symptoms/selected', [PatientSymptomController::class, 'selected']);
    Route::post('patient-symptoms', [PatientSymptomController::class, 'save']);
    Route::post('patient-symptoms/add', [PatientSymptomController::class, 'add']);
    Route::post('patient-symptoms/sync', [PatientSymptomController::class, 'save']);
    
    // Health Parameters API
    Route::get('patient-health-parameters', [PatientHealthParameterController::class, 'index']);
    Route::get('patient-health-parameters/selected', [PatientHealthParameterController::class, 'selected']);
    Route::post('patient-health-parameters', [PatientHealthParameterController::class, 'save']);
});
