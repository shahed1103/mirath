<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use App\Models\UserDevice;

class FirebaseService
{
    /**
     * Send notification to all user devices.
     */
    public function sendToDevices(Collection $devices , string $title , string $body , string $type , array $data=[]): void{

        foreach ($devices as $device) {

            try {
                $this->sendToToken($device->fcm_token , $title , $body , $type, $data);
            } catch (Throwable $e) {
                Log::error('Firebase Notification Failed', [
                    'user_id' => $userId,
                    'device_id' => $device->id,
                    'token' => $device->fcm_token,
                    'error' => $e->getMessage(),
                ]);

                if ($this->isInvalidToken($e->getMessage())) {
                    $device->delete();
                }
            }
        }
    }

    /**
     * Send notification to one device.
     */
    public function sendToToken(string $token , string $title , string $body , string $type , array $data = [] ): array {
            $response = Http::timeout(10)
                ->withToken($this->getAccessToken())
                ->post(
                    "https://fcm.googleapis.com/v1/projects/" .
                     env('FIREBASE_PROJECT_ID') .
                    "/messages:send",
                $this->buildPayload($token, $title , $body , $type , $data)
                );

            if(!$response->successful()){
                throw new \Exception( $response->body() );
            }

        return $response->json();
    }

    public function sendToTopic(string $topic , string $title , string $body , string $type , array $data = []): array {
        $response = Http::timeout(10)
            ->withToken($this->getAccessToken())
            ->post(
                "https://fcm.googleapis.com/v1/projects/"
                . env('FIREBASE_PROJECT_ID')
                . "/messages:send",

                [

                    "message" => [

                        "topic" => $topic,


                        "notification" => [

                            "title" => $title,

                            "body" => $body,

                        ],


                        "data" => array_merge(

                            [
                                "type" => $type
                            ],

                            collect($data)
                                ->map(fn($value)=>(string)$value)
                                ->toArray()

                        )

                    ]

                ]

            );

        if(!$response->successful()){
            throw new \Exception( $response->body() );
        }
        return $response->json();
    }

    /**
     * Build FCM payload.
     */
    private function buildPayload(string $token , string $title , string $body , string $type , $data = []): array {
        return [

            "message" => [

                "token" => $token,

                "notification" => [

                    "title" => $title,

                    "body" => $body,

                ],

                "data" => array_merge(

                    [

                        "type" => $type

                    ],

                    collect($data)
                        ->map(fn ($value) => (string) $value)
                        ->toArray()

                )

            ]

        ];

    }
   
    /**
     * Get Firebase access token.
     */
    private function getAccessToken(): string{
        return Cache::remember('firebase_access_token', now()->addMinutes(55),
            function () {
                $client = new GoogleClient();
                $client->setAuthConfig(
                    storage_path(
                        'app/firebase/mirath-485d7-firebase-adminsdk-fbsvc-a9c41859ae.json'
                    )
                );

                $client->addScope(
                    'https://www.googleapis.com/auth/firebase.messaging'
                );

                $client->refreshTokenWithAssertion();

                return $client
                    ->getAccessToken()['access_token'];
            }
        );
    }

    /**
     * Detect invalid device token.
     */
    private function isInvalidToken(string $message): bool {
        return str_contains($message, 'UNREGISTERED')

            || str_contains($message, 'registration-token-not-registered')

            || str_contains($message, 'Requested entity was not found');
    }
}
