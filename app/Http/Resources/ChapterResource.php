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


        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_open' => $isUnlocked ? 'مفتوح' : 'مغلق',
            // 'is_unlocked' => $this->order_number == 1 ? true : ($this->progress?->first()->is_unlocked ?? false), 
        ];
    }
}
