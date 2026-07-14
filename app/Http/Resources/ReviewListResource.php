<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class ReviewListResource extends JsonResource
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
            'classification' => $this->book->classification->classification,
            'book_name' => $this->book->title,
            'book_photo' => url(Storage::url($this->book->photo)),
            'chapter_name' => $this->title,
        ];
    }
}
