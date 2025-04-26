<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\SystemSetting;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseService
{
    // public function sendNotification($title, $message, $tokens)
    // {
    //     $url = "https://fcm.googleapis.com/fcm/send";
    //     $serverKey = config('services.firebase.server_key');

    //     $data = [
    //         "registration_ids" => is_array($tokens) ? $tokens : [$tokens],
    //         "notification" => [
    //             "title" => $title,
    //             "body" => $message,
    //             "sound" => "default",
    //         ]
    //     ];

    //     $response = Http::withHeaders([
    //         "Authorization" => "key=$serverKey",
    //         "Content-Type" => "application/json"
    //     ])->post($url, $data);

    //     return $response->json();
    // }

    public function sendNotification(string $title, string $message, array $tokens): void
    {
        try {
            $serviceAccountPath = public_path('masjid-suite-firebase-adminsdk-geodk-dd6693d7aa.json');

            if (!file_exists($serviceAccountPath)) {
                Log::error("Firebase service account file not found at path: {$serviceAccountPath}");
                return;
            }

            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            foreach ($tokens as $token) {
                $notification = Notification::create($title, Str::limit($message, 100));

                $cloudMessage = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification);

                $messaging->send($cloudMessage);

                Log::info("Notification sent to token: {$token}");
            }
        } catch (\Exception $exception) {
            Log::error('Firebase notification error: ' . $exception->getMessage());
        }
    }
}
