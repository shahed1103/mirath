<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\Response;

class AddQuestionRequest extends FormRequest
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
            'question_text' => 'required|string|unique:questions,question_text',
            'explanation' => 'required|string',
            'difficulty_score' => 'required|numeric|min:100|max:900',

            'answers' => 'required|array|min:2|max:4',

            'answers.*.answer_text' => 'required|string',

            'answers.*.is_correct' => 'required|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {

            $answers = $this->answers ?? [];

            $correctAnswers = collect($answers)
                ->where('is_correct', true)
                ->count();

            if ($correctAnswers !== 1) {
                $validator->errors()->add(
                    'answers',
                    'A question must have exactly one correct answer.'
                );
            }
        });
    }


    protected function failedValidation(Validator $validator){

        //Throw a validationexception eith the translated error messages
        $message = "you have sent invalid data";

        throw new ValidationException($validator, Response::Validation([], $message ,$validator->errors()));
    }
}
