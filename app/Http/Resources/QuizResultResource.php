<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResultResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     public function toArray(Request $request): array
    {
        return [
            'success' => $this->success ,
            'correct_answers' => $this->correct_answers,
            'correct_answers_pricent' =>
              round(($this->correct_answers * 100) / 5),
            'new_points' =>
                $this->points,
            'all_user_points' =>
                auth()->user()->points,
            'total_questions' => $this->additional['total_questions'] ?? null,
        ];
    }

}
