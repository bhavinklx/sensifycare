<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientReminder;
use App\Models\PatientReport;
use App\Models\HealthParameter;
use App\Models\PatientHealthParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientReminderController extends Controller
{
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

    /**
     * Generates dynamic system reminders for a patient based on database state.
     */
    private function getDynamicAutomatedReminders($patient)
    {
        $reminders = [];
        $idCounter = 100000;

        $addReminder = function($title, $dateText, $label, $iconType) use (&$reminders, &$idCounter, $patient) {
            $reminders[] = [
                'patient_reminder_id' => ++$idCounter,
                'patient_id' => $patient->patient_id,
                'title' => $title,
                'date_text' => $dateText,
                'label' => $label,
                'icon_type' => $iconType,
                'type' => 'automated',
                'is_completed' => false,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];
        };

        // 1. Profile Completeness
        $profileFields = [
            'patient_fname', 'patient_lname', 'patient_dob', 'patient_gender', 
            'height_cm', 'weight_kg', 'patient_city'
        ];
        $incomplete = false;
        foreach ($profileFields as $field) {
            if (empty($patient->$field)) {
                $incomplete = true;
                break;
            }
        }
        if ($incomplete) {
            $addReminder(
                'Complete basic health profile',
                'Action Needed',
                'Recommended',
                'profile'
            );
        }

        // 2. Lab Reports Upload Check
        $reportsCount = PatientReport::where('patient_id', $patient->patient_id)->count();
        if ($reportsCount === 0) {
            $addReminder(
                'Upload your first lab report',
                'Due Soon',
                'Recommended',
                'test'
            );
        } else {
            // 3. Follow-up on abnormal markers
            $latestReport = PatientReport::where('patient_id', $patient->patient_id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($latestReport && $latestReport->abnormal_count > 0) {
                $count = $latestReport->abnormal_count;
                $addReminder(
                    "Check with doctor regarding {$count} abnormal " . ($count === 1 ? "marker" : "markers"),
                    'Due Soon',
                    'Suggested',
                    'doctor'
                );
            }
        }

        // 4. Lifestyle Habits assessment incomplete
        $activeParams = HealthParameter::active()->get();
        $answersCount = PatientHealthParameter::where('patient_id', $patient->patient_id)->count();
        if ($answersCount < $activeParams->count()) {
            $addReminder(
                'Complete your lifestyle assessment checklist',
                'Due Soon',
                'Suggested',
                'calendar'
            );
        }

        return $reminders;
    }
}
