<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\Response;

class UserSigninRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // إذا الإيميل والباسورد الاثنين غير موجودين
        if (
            $this->missing('email') &&
            $this->missing('password')
        ) {
            $message = 'Email and password are required';
        } else {
            $message = 'you have sent invalid data';
        }

        throw new ValidationException(
            $validator,
            Response::Validation(
                [],
                $message,
                $errors
            )
        );
    }
}