<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan' => 'required|in:free,regular,premium',
            'package_type' => 'required|in:single_course,all_in_one',
            'courses_ids' => 'nullable|array',
            'courses_ids.*' => 'exists:courses,id',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:months,years',
            'price' => 'required|numeric|min:0',
            'auto_renew' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan.required' => 'Please select a subscription plan',
            'plan.in' => 'Invalid subscription plan selected',
            'package_type.required' => 'Please select a package type',
            'courses_ids.*.exists' => 'One or more selected courses do not exist',
            'duration.required' => 'Subscription duration is required',
            'duration.min' => 'Duration must be at least 1',
            'price.required' => 'Price is required',
            'price.min' => 'Price cannot be negative',
            'start_date.required' => 'Start date is required',
            'end_date.after' => 'End date must be after start date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-renew defaults to false if not provided
        if (!$this->has('auto_renew')) {
            $this->merge(['auto_renew' => false]);
        }
    }
}
