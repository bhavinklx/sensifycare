<?php

namespace App\Traits;

use App\Models\Patient;
use App\Models\PatientReport;
use App\Models\HealthParameter;
use App\Models\PatientHealthParameter;

trait GeneratesDynamicReminders
{
    /**
     * Generates dynamic system reminders for a patient based on database state.
     */
    protected function getDynamicAutomatedReminders(Patient $patient)
    {
        $reminders = [];
        $idCounter = 100000; // Use a distinct range of IDs for dynamic items to prevent collision

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
