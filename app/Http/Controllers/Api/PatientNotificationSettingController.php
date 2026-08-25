<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientNotificationSettingController extends Controller
{
    /**
     * Fetch the patient's notification settings.
     */
    public function show(Request $request)
    {
        $patient = $request->user();

        $setting = PatientNotificationSetting::firstOrCreate(
            ['patient_id' => $patient->patient_id]
        );

        $setting->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Notification settings fetched successfully',
            'data' => $setting
        ], 200);
    }

    /**
     * Update the patient's notification settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'abnormal_marker_alert' => 'sometimes|boolean',
            'report_ready' => 'sometimes|boolean',
            'ai_health_insights' => 'sometimes|boolean',
            'lab_test_reminders' => 'sometimes|boolean',
            'weekly_health_digest' => 'sometimes|boolean',
            'health_tips_articles' => 'sometimes|boolean',
            'app_updates' => 'sometimes|boolean',
            'offers_promotions' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = $request->user();
        $setting = PatientNotificationSetting::firstOrCreate(
            ['patient_id' => $patient->patient_id]
        );

        $setting->refresh();

        $setting->update($request->only([
            'abnormal_marker_alert',
            'report_ready',
            'ai_health_insights',
            'lab_test_reminders',
            'weekly_health_digest',
            'health_tips_articles',
            'app_updates',
            'offers_promotions'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully',
            'data' => $setting->fresh()
        ], 200);
    }
}
