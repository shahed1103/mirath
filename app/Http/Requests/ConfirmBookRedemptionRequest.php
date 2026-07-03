<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmBookRedemptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

        'book_ids' => ['required', 'array', 'min:1'],
'book_ids.*' => ['exists:library_books,id'],

        ];
    }
}
