<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPackageRequest extends FormRequest
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
            'package_type' => ['required', 'in:single_course,all_in_one'],
            'duration' => ['required', 'integer', 'in:1,3,12'],
            'duration_unit' => ['required', 'in:months,years'],
            'courses_ids' => ['required_if:package_type,single_course', 'array'],
            'courses_ids.*' => ['integer', 'exists:courses,id'],
        ];
    }
}
