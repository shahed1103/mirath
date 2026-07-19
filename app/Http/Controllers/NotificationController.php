<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Http\Requests\Notification\DeviceFcmRequest;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\UserDevice;
use App\Http\Responses\response;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    public function __construct(NotificationService $notificationService = null){
        $this->notificationService = $notificationService ?? new NotificationService();
    }

    public function deviceFcmToken(DeviceFcmRequest $request): JsonResponse {
      $data = [] ;
        try{
            $data = $this->notificationService->deviceFcmToken($request);
           return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function getUserNotifications($userId): JsonResponse {
      $data = [] ;
        try{
            $data = $this->notificationService->getUserNotifications($userId);
           return Response::Success($data['notifications'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getUnreadCount($userId): JsonResponse {
      $data = [] ;
        try{
            $data = $this->notificationService->getUnreadCount($userId);
           return Response::Success($data['unread_notifications_count'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function sendFcmNotification(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'type'  => 'required|string',
            'data'  => 'required|string',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $fcm = $user->fcm_token;

        if (!$fcm) {
            return response()->json(['message' => 'User does not have a device token'], 400);
        }

        $title = $request->title;
        $description = $request->body;

        // -----------------------------
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $description,
            'type' => $request->type, 
            'data' => $request->data,
        ]);
        // -----------------------------

        $projectId = env('FCM_PROJECT_ID');
        $credentialsFilePath = storage_path('app/firebase/mirath-485d7-firebase-adminsdk-fbsvc-a9c41859ae.json');

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                    "type" => $request->type, 
                    "data" => $request->data,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent and stored',
                'response' => json_decode($response, true)
            ]);
        }
    }

}