<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\response;



class AddNewChapterRequest extends FormRequest
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
        return [
            //Chapter validation rules
            'chapter_title' => 'required|string|max:255|unique:chapters,title',
            'start_page' => 'required|integer|min:1',
            'end_page' => 'required|integer|min:1',     
                   
            //Chapter content validation rules
            'audio_url' => 'required|file|mimes:mp3,wav,m4a,mp4',
            'pdf_url' => 'required|file|mimes:pdf',
            'video_url' => ['required', 'url', 'regex:/^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/'],

            'audio_length' => 'required|integer|min:1',
            'video_length' => 'required|integer|min:1',
            'pdf_length' => 'required|integer|min:1',
        ];
    }



    protected function failedValidation(Validator $validator){

        //Throw a validationexception eith the translated error messages
        $message = "you have sent invalid data";

        throw new ValidationException($validator, Response::Validation([], $message ,$validator->errors()));
    }
}
