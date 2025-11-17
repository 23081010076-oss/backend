<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNeedAssessmentRequest extends FormRequest
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
            'form_data' => ['required', 'array'],
            'form_data.learning_goals' => ['required', 'string', 'max:500'],
            'form_data.previous_experience' => ['required', 'string', 'max:500'],
            'form_data.challenges' => ['required', 'string', 'max:500'],
            'form_data.expectations' => ['required', 'string', 'max:500'],
            'completed_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
