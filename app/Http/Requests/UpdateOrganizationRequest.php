<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'founded_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'logo_url' => 'nullable|url',
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
            'name.max' => 'Organization name cannot exceed 255 characters',
            'website.url' => 'Please provide a valid website URL',
            'contact_email.email' => 'Please provide a valid email address',
            'founded_year.min' => 'Founded year cannot be before 1800',
            'founded_year.max' => 'Founded year cannot be in the future',
            'logo_url.url' => 'Please provide a valid logo URL',
        ];
    }
}
