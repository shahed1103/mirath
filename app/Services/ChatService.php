<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Nationality;
use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Http;

class ChatService {

    public function chat($request): array{
        $response = Http::timeout(120)
            ->post(
            env('RAG_API_URL'),
            [
                'question' => $request->question
            ]
        );

        if (!$response->successful()) {
            return [
                'answer' => 'حدث خطأ أثناء الاتصال بمحرك الذكاء الاصطناعي.',
                'sources' => []
            ];
        }

        $result = $response->json();

        $answer['answer']= $result['answer'] ?? '';
        $answer['sources']= $result['sources'] ?? [];
        return [
            'data' => $answer,
            'message' => 'Answer retrieved successfully',
        ];

    }
}