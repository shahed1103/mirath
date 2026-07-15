<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class BookRedemptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'student' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'book' => [
                'id' => $this->book->id,
                'name' => $this->book->name,
                'author' => $this->book->author,
            ],

            'points_spent' => $this->points_spent,
            'status' => $this->status,
            'redeemed_at' => $this->created_at,
        ];
    }
}
