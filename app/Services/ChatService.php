<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatService {

    public function getAllChats(): array {
        $chats = Chat::where('user_id', Auth::id())
            ->select('id', 'title')
            ->latest()
            ->get();

        return [
            'data' => $chats,
            'message' => 'Chats retrieved successfully.'
        ];
    }

    public function getChatMessages($chatId): array{
        $chat = Chat::where('id', $chatId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'role' => $message->role,
                    'message' => $message->message,
                    'sources' => $message->sources ?? [],
                ];
            });

        return [
            'data' => $messages,
            'message' => 'Messages retrieved successfully.'
        ];
    }

    public function chat($request): array{

        if (!$request->chat_id) {

            $chat = Chat::create([
                'user_id' => Auth::id(),
                'title' => Str::limit(trim($request->question), 30, '...')
            ]);

            $chatId = $chat->id;

        } else {
            $chat = Chat::where('id', $request->chat_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $chatId = $chat->id;
        }

        Message::create([
            'chat_id' => $chatId,
            'role' => 'user',
            'message' => $request->question
        ]);

        $history = Message::where('chat_id', $chatId)
            ->orderByDesc('id')
            ->take(6)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->message
                ];
        })
        ->values();

        $response = Http::timeout(120)
            ->post(
            env('RAG_API_URL'),
            [
                'question' => $request->question,
                'history' => $history
            ]
        );

        if (!$response->successful()) {
            return [
                'data' => [
                    'answer' => 'حدث خطأ أثناء الاتصال بمحرك الذكاء الاصطناعي.',
                    'sources' => []
                ],
                'message' => 'Request failed.'
            ];
        }

        $result = $response->json();

        Message::create([
            'chat_id' => $chatId,
            'role' => 'assistant',
            'message' => $result['answer'] ?? '',
            'sources' => $result['sources'] ?? []
        ]);

        return [
            'data' => [
                'chat_id' => $chatId,
                'answer' => $result['answer'] ?? '',
                'sources' => $result['sources'] ?? []
            ],
            'message' => 'Answer retrieved successfully',
        ];

    }

}