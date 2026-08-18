<?php

namespace App\Services;

use Firebase\JWT\JWT;

class JaaSService
{
    public function generateToken(
        string $roomName,
        int $userId,
        string $userName,
        ?string $userEmail = null,
        bool $moderator = false
    ): string {
        $appId = config('services.jaas.app_id');
        $apiKeyId = config('services.jaas.api_key_id');
        $privateKeyPath = config('services.jaas.private_key_path');

        $privateKey = file_get_contents(
            base_path($privateKeyPath)
        );

        $now = time();

        $payload = [
            'aud' => 'jitsi',
            'iss' => 'chat',
            'sub' => $appId,
            'room' => $roomName,

            'exp' => $now + 3600,
            'nbf' => $now - 10,

            'context' => [
                'user' => [
                    'id' => (string) $userId,
                    'name' => $userName,
                    'email' => $userEmail,
                    'moderator' => $moderator,
                ],
            ],
        ];

        return JWT::encode(
            $payload,
            $privateKey,
            'RS256',
            $apiKeyId
        );
    }
}