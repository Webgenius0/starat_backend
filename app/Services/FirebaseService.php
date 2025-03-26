<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    public function sendNotification($title, $message, $tokens)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = config('services.firebase.server_key');

        $data = [
            "registration_ids" => is_array($tokens) ? $tokens : [$tokens],
            "notification" => [
                "title" => $title,
                "body" => $message,
                "sound" => "default",
            ]
        ];

        $response = Http::withHeaders([
            "Authorization" => "key=$serverKey",
            "Content-Type" => "application/json"
        ])->post($url, $data);

        return $response->json();
    }
}
