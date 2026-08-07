<?php

namespace App\Http\Requests\StudyPlan;

use Illuminate\Foundation\Http\FormRequest;

class StudyPlanRequest extends FormRequest
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
                'required',
                'exists:books,id'
            ],

            'plan_type' => [
                'required',
                'in:duration,daily_pages'
            ],

            'target_days' => [
                'nullable',
                'required_if:plan_type,duration',
                'integer',
                'min:1'
            ],

            'daily_pages' => [
                'nullable',
                'required_if:plan_type,daily_pages',
                'integer',
                'min:1'
            ],

            'study_days' => [
                'required',
                'array',
                'min:1'
            ],

            'study_days.*' => [
                'integer',
                'between:0,6'
            ],

            'notification_time' => [
                'required',
                'date_format:H:i'
            ],

            'offline' => [
                'required',
                'boolean'
            ]

        ];
    }
}
