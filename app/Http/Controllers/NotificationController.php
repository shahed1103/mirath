<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Http\Requests\Notification\DeviceFcmRequest;
use App\Http\Requests\Notification\SendNotificationRequest;
use App\Services\NotificationService;
use App\Services\NotificationManager;
use App\Services\DeviceService;
use App\Http\Responses\response;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    public function __construct(
        private NotificationService $notificationService,
        private NotificationManager $notificationManager,
        private DeviceService $deviceService
    ) {}
    
    public function saveDeviceToken(DeviceFcmRequest $request): JsonResponse{
        $data = [] ;
        try{
            $data = $this->deviceService->saveDeviceToken($request->validated());
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors);
        }
    }

    public function getNotifications(): JsonResponse{
        $data = [] ;
        try{
            $data = $this->notificationService->getNotifications(auth()->id());
            return Response::Success($data['notifications'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors);
        }
    }

    public function getUnreadCount(): JsonResponse{
        $data = [] ;
        try{
            $data = $this->notificationService->getUnreadCount(auth()->id());
            return Response::Success($data['unread_notifications_count'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors);
        }
    }

    public function markAllAsRead(): JsonResponse{
        $data = [] ;
        try{
            $data = $this->notificationService->markAllAsRead(auth()->id());
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors);
        }
    }

    public function sendNotification(SendNotificationRequest $request): JsonResponse{
        $data = [] ;
        try{
            $data = $this->notificationManager->sendNotification($request);
            return Response::Success($data['notification'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors);
        }
    }


// public function test()
// {

//     $this->notificationManager->send(

//         3,

//         'Test Title',

//         'Hello from Laravel',

//         'test',

//         [
//             'screen'=>'home',
//             'id'=>10
//         ]

//     );


//     return response()->json([

//         'message'=>'sent'

//     ]);

// }


}