<?php

// namespace App\Http\Resources;

// use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\JsonResource;
// use Storage;
// class BookDetailsResource extends JsonResource
// {
//     /**
//      * Transform the resource into an array.
//      *
//      * @return array<string, mixed>
//      */
//     public function toArray(Request $request): array
//     {
//         $photoUrl = null;

//         if (
//             $this->photo_upload_status === 'uploaded' &&
//             !empty($this->photo)
//         ) {
//             $photoUrl = url(Storage::url($this->photo));
//         }

//         return [
//             // 'photo' => url(Storage::url($this->photo)),
//             'photo' => $photoUrl,
//             'photo_upload_status' => $this->photo_upload_status,
//             'title' => $this->title,
//             'author_name' => $this->author_name,
//             'total_pages' => $this->total_pages,
//             'total_chapters' => $this->chapters_count,
//             'bio' => $this->bio,
//         ];
//     }
// }

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photoUrl = null;

        /*
        |--------------------------------------------------------------------------
        | Old and new books
        |--------------------------------------------------------------------------
        |
        | Old books have photo but photo_upload_status = null.
        | New books have photo = null while upload is pending.
        |
        */

        if (!empty($this->photo)) {
            $photoUrl = url(
                Storage::url($this->photo)
            );
        }

        $uploadStatus = $this->photo_upload_status;

        /*
        |--------------------------------------------------------------------------
        | Old existing photos
        |--------------------------------------------------------------------------
        */

        if (
            empty($uploadStatus) &&
            !empty($this->photo)
        ) {
            $uploadStatus = 'uploaded';
        }

        return [
            'photo' => $photoUrl,
            'photo_upload_status' => $uploadStatus,

            'title' => $this->title,
            'author_name' => $this->author_name,
            'total_pages' => $this->total_pages,
            'total_chapters' => $this->chapters_count,
            'bio' => $this->bio,
        ];
    }
}