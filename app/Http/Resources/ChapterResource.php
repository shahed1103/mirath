<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $isUnlocked = $this->order_number == 1
        || ($this->progress?->first()->is_open ?? false);

        $passedExam = $this->exams()
            ->where('user_id', auth()->id())
            ->where('success', true)
            ->latest()
            ->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $isUnlocked ? 'مفتوح' : 'مغلق',
            'exam status' => (bool) $passedExam ,
        ];
    }
}
