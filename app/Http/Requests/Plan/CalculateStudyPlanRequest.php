<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class CalculateStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'book_ids' => [
                'required',
                'array',
                'min:1'
            ],

            'book_ids.*' => [
                'exists:books,id'
            ],

            'plan_type' => [
                'required',
                'in:duration,daily_pages'
            ],

            'target_days' => [
                'required_if:plan_type,duration',
                'nullable',
                'integer',
                'min:1'
            ],

            'daily_pages' => [
                'required_if:plan_type,daily_pages',
                'nullable',
                'integer',
                'min:1'
            ]

        ];
    }
}
