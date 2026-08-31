<?php

namespace App\Services;

use App\Models\UserDevice;
use App\Models\PatientNotification;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send FCM Push Notification to a patient
     *
     * @param int $patientId
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @return bool
     */
    public function sendToPatient($patientId, $title, $body, $data = null)
    {
        // Save to Database so patient can view history
        $notification = PatientNotification::create([
            'patient_id' => $patientId,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        // Get devices to send push notification
        $devices = UserDevice::where('user_id', $patientId)
            ->where('user_type', 'patient')
            ->whereNotNull('push_notification_id')
            ->get();

        if ($devices->isEmpty()) {
            return false;
        }

        try {
            // Setup Google Client for FCM Auth
            $client = new GoogleClient();
            $client->setAuthConfig(base_path('sensify-care-f25e9c3aa06c.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            
            $token = $client->getAccessToken();
            $accessToken = $token['access_token'];

            $projectId = 'sensify-care'; // Change if different in JSON

            foreach ($devices as $device) {
                $fcmToken = $device->push_notification_id;

                $payload = [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data ? (array) $data : (object)[],
                    ]
                ];

                $response = Http::withToken($accessToken)
                    ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

                if ($response->failed()) {
                    Log::error("FCM Send Failed: " . $response->body());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("NotificationService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send FCM Push Notification to a specific token (Useful for testing)
     */
    public function sendToToken($fcmToken, $title, $body, $data = null)
    {
        try {
            $client = new GoogleClient();
            $client->setAuthConfig(base_path('sensify-care-f25e9c3aa06c.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            
            $token = $client->getAccessToken();
            $accessToken = $token['access_token'];

            $projectId = 'sensify-care';

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data ? (array) $data : (object)[],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->failed()) {
                Log::error("FCM Send Failed: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("NotificationService Error (sendToToken): " . $e->getMessage());
            return false;
        }
    }
}
