<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'type' => [
                'required',
                Rule::in([
                    'daily_amount',
                    'fixed_duration'
                ])
            ],

            'daily_chapters' => [
                'required_if:type,daily_amount',
                'nullable',
                'integer',
                'min:1'
            ],

            'duration_days' => [
                'required_if:type,fixed_duration',
                'nullable',
                'integer',
                'min:1'
            ],

            'book_ids' => [
                'required',
                'array',
                'min:1'
            ],

            'book_ids.*' => [
                'exists:books,id'
            ],

            'study_days' => [
                'required',
                'array',
                'min:1'
            ],

            'study_days.*' => [
                Rule::in([
                    'saturday',
                    'sunday',
                    'monday',
                    'tuesday',
                    'wednesday',
                    'thursday',
                    'friday'
                ])
            ],

            'notification_time' => [
                'required',
                'date_format:H:i'
            ],

            'is_offline' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'daily_chapters.required_if' =>
                'Daily chapters value is required.',

            'duration_days.required_if' =>
                'Duration days value is required.',

            'book_ids.required' =>
                'Please select at least one book.',

            'study_days.required' =>
                'Please select at least one study day.'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_offline')) {
            $this->merge([
                'is_offline' => filter_var(
                    $this->is_offline,
                    FILTER_VALIDATE_BOOLEAN
                )
            ]);
        }
    }
}
