<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;
class BookDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'photo' => url(Storage::url($this->photo)),
            'title' => $this->title,
            'author_name' => $this->author_name,
            'total_pages' => $this->total_pages,
            'total_chapters' => $this->chapters_count,
            'bio' => $this->bio,
        ];
    }
}
