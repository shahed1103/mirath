<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Chat\ChatRequest;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService  $chatService){
        $this->chatService = $chatService;
    }

    public function getAllChats(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chatService->getAllChats();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getChatMessages($chatId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chatService->getChatMessages($chatId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
    
    public function chat(ChatRequest $request): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chatService->chat($request);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}