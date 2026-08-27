<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientNotification;
use Illuminate\Http\Request;

class PatientNotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated patient.
     */
    public function index(Request $request)
    {
        $patient = $request->user();

        $notifications = PatientNotification::where('patient_id', $patient->patient_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Notifications fetched successfully',
            'data' => $notifications
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $patient = $request->user();

        $notification = PatientNotification::where('patient_notification_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read successfully'
        ], 200);
    }
}
