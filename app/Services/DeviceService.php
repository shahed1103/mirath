<?php

namespace App\Services;

use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Exception;

class DeviceService
{

    public function saveDeviceToken(array $data): array {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('Unauthenticated.', 401);
        }

        UserDevice::updateOrCreate(
            [
                'user_id'   => $user->id,
                'fcm_token' => $data['fcm_token'],
            ],
            [
                'device_type' => $data['device_type'],
            ]
        );

        return [
            'data' => null,
            'message' => 'Device registered successfully.'
        ];
    }

    public function getUserDevices(int $userId){
        return UserDevice::where('user_id', $userId)->get();
    }
}