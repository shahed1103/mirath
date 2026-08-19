<?php

// namespace App\Http\Resources;

// use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\JsonResource;
// use Storage; 
// class BookResource extends JsonResource
// {
//     public function toArray(Request $request): array
//     {
//         return [
//             'id' => $this->id,
//             'title' => $this->title,
//             'author_name' => $this->author_name,
//             'photo' => url(Storage::url($this->photo))
//         ];
//     }
// }


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photoUrl = null;

        /*
        |--------------------------------------------------------------------------
        | Existing old photos
        |--------------------------------------------------------------------------
        | Old books may have a photo path but photo_upload_status = null.
        | If photo exists, we can safely treat it as uploaded.
        */

        if (!empty($this->photo)) {
            $photoUrl = url(
                Storage::url($this->photo)
            );
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'author_name' => $this->author_name,
            'photo' => $photoUrl,

            'photo_upload_status' =>
                $this->photo_upload_status
                ?? (!empty($this->photo) ? 'uploaded' : null),
        ];
    }
}