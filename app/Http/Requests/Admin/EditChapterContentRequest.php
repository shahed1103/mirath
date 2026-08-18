<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\Response;



class EditChapterContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $content = \App\Models\ChapterContent::find($this->route('contentId'));

        // $type = $this->filled('type')
        //     ? $this->input('type')
        //     : $content->type;

        $type = $content->type;

        $urlRules = match ($type) {
            'pdf' => 'nullable|file|mimes:pdf',

            'audio' => 'nullable|file|mimes:mp3,wav,m4a,mp4',

            'video' => ['nullable', 'url', 'regex:/^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/'],

            default => ['nullable']
        };

        return [
            'type' => 'nullable|string|in:pdf,audio,video',
            'url'  => $urlRules,
        ];
    }



    protected function failedValidation(Validator $validator){

        //Throw a validationexception eith the translated error messages
        $message = "you have sent invalid data";

        throw new ValidationException($validator, Response::Validation([], $message ,$validator->errors()));
    }
}
