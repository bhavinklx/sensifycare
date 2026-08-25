<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientReminder;
use App\Traits\GeneratesDynamicReminders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientReminderController extends Controller
{
    use GeneratesDynamicReminders;

    /**
     * Get patient reminders split into pending and completed.
     */
    public function index(Request $request)
    {
        $patient = $request->user();

        // 1. Fetch custom reminders stored in database
        $pendingCustom = PatientReminder::where('patient_id', $patient->patient_id)
            ->where('is_completed', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $completed = PatientReminder::where('patient_id', $patient->patient_id)
            ->where('is_completed', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        // 2. Fetch on-the-fly dynamic automated reminders
        $automated = $this->getDynamicAutomatedReminders($patient);

        // Merge custom pending with automated pending
        $pending = array_merge($automated, $pendingCustom);

        return response()->json([
            'success' => true,
            'message' => 'Reminders fetched successfully',
            'data' => [
                'pending' => $pending,
                'completed' => $completed
            ]
        ], 200);
    }

    /**
     * Create a custom reminder.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date_text' => 'required|string|max:100',
            'label' => 'nullable|string|max:50',
            'icon_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $reminder = $request->user()->reminders()->create([
            'title' => $request->title,
            'date_text' => $request->date_text,
            'label' => $request->label,
            'icon_type' => $request->input('icon_type', 'general'),
            'type' => 'custom',
            'is_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder created successfully',
            'data' => $reminder
        ], 201);
    }

    /**
     * Toggle reminder completion status.
     */
    public function toggle(Request $request, $id)
    {
        $reminder = PatientReminder::where('patient_id', $request->user()->patient_id)
            ->where('patient_reminder_id', $id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder not found.'
            ], 404);
        }

        $reminder->is_completed = !$reminder->is_completed;
        $reminder->save();

        return response()->json([
            'success' => true,
            'message' => 'Reminder status toggled successfully.',
            'data' => $reminder
        ], 200);
    }

    /**
     * Delete a reminder.
     */
    public function destroy(Request $request, $id)
    {
        $reminder = PatientReminder::where('patient_id', $request->user()->patient_id)
            ->where('patient_reminder_id', $id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder not found.'
            ], 404);
        }

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder deleted successfully.'
        ], 200);
    }
}
