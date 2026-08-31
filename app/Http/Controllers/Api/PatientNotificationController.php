<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientNotification;
use App\Services\NotificationService;
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
            'data' => [
                'current_page' => $notifications->currentPage(),
                'data' => $notifications->items(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
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

    /**
     * Send a test notification to the authenticated patient.
     */
    public function sendTest(Request $request)
    {
        $patient = $request->user();
        
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        $notificationService = new NotificationService();
        
        if ($request->has('fcm_token') && !empty($request->input('fcm_token'))) {
            $success = $notificationService->sendToToken(
                $request->input('fcm_token'),
                $request->input('title'),
                $request->input('body'),
                $request->input('data', null)
            );
        } else {
            $success = $notificationService->sendToPatient(
                $patient->patient_id,
                $request->input('title'),
                $request->input('body'),
                $request->input('data', null)
            );
        }

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification. Ensure the patient has a valid push token.'
            ], 400);
        }
    }
}
