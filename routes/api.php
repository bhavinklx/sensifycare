<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;


use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PatientReportController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PatientReminderController;
use App\Http\Controllers\Api\PatientNotificationSettingController;
use App\Http\Controllers\Api\PatientNotificationController;

/**
 * Public Routes
 */
Route::get('pages', [PageController::class, 'index']);
/**
 * Dashboard Routes
 */
Route::post('/analyze-report', [DashboardController::class, 'analyzeReport'])->name("analyze-report");
Route::post('/analyze-report-upload', [DashboardController::class, 'uploadReport'])->name("analyze-report-upload");

/**
 * Patient Auth Routes
 */

Route::post('patient-signup', [PatientAuthController::class, 'signup']);
Route::post('patient-send-otp', [PatientAuthController::class, 'sendOtp']);
Route::post('patient-verify-otp', [PatientAuthController::class, 'verifyOtp']);

/**
 * Protected Routes
 */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('patient-logout', [PatientAuthController::class, 'logout']);
    
    /**
     * Profile Routes
     */
    Route::get('patient-profile', [PatientAuthController::class, 'getProfile']);
    Route::post('patient-profile/update', [PatientAuthController::class, 'updateProfile']);
    Route::delete('patient-profile/delete', [PatientAuthController::class, 'deleteAccount']);
    Route::post('patient-dashboard', [PatientAuthController::class, 'getDashboard']);
    Route::get('patient-reminders', [PatientReminderController::class, 'index']);
    Route::post('patient-reminders', [PatientReminderController::class, 'store']);
    Route::post('patient-reminders/{id}/toggle', [PatientReminderController::class, 'toggle']);
    Route::delete('patient-reminders/{id}', [PatientReminderController::class, 'destroy']);
    Route::get('patient-notification-settings', [PatientNotificationSettingController::class, 'show']);
    Route::post('patient-notification-settings', [PatientNotificationSettingController::class, 'update']);
    Route::get('patient-notifications', [PatientNotificationController::class, 'index']);
    Route::post('patient-notifications/{id}/mark-read', [PatientNotificationController::class, 'markAsRead']);

    /**
     * Article Routes
     */
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);

    /**
     * Report Upload Routes
     */
    Route::get('patient-reports', [PatientReportController::class, 'index']);
    Route::post('patient-reports/upload', [PatientReportController::class, 'upload']);
    Route::get('patient-reports/recent', [PatientReportController::class, 'recent']);
    Route::post('patient-reports/analyze', [PatientReportController::class, 'analyze']);
});
