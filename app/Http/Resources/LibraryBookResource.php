<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LibraryBookResource extends JsonResource
{

public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'author' => $this->author,
        'price' => $this->price,
        'count' => $this->count,
        'photo' => $this->photo
            ? Storage::disk('r2')->url($this->photo)
            : null,
    ];
}
}
