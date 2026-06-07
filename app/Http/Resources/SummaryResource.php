<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'chapter_title' => $this->chapter?->title,

            'summary_title' => $this->title,

            'content_preview' => mb_strimwidth($this->content, 0, 120, '...'),

            'created_at' => $this->created_at?->format('Y-m-d H:i'),

            'edited' => (bool) $this->edited,
            // $this->created_at != $this->updated_at,

            'edited_at' => $this->edited_at
                ? $this->edited_at->format('Y-m-d H:i')
                : 0,
        ];

    }
}
