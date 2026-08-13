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

        $chapterProgress = 0;

        if ($this->contents->count() > 0) {

            $totalProgress = 0;

            foreach ($this->contents as $content) {

                $userProgress = optional(
                    $content->progresses->first()
                )->progress ?? 0;

                if ($content->total_progress_value > 0) {

                    $totalValue = $content->total_progress_value;

                    if (in_array($content->type, ['video', 'audio'])) {
                        $totalValue *= 60;
                    }

                    $totalProgress +=
                        ($userProgress / $totalValue) * 100;
                }
            }

            $chapterProgress =
                $totalProgress / $this->contents->count();
        }

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
            'study_progress' => round($chapterProgress, 2),
        ];
    }
}
