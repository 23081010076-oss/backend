<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressReportRequest extends FormRequest
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
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'report_date' => ['required', 'date_format:Y-m-d'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['required', 'string', 'max:1000'],
            'attachment_url' => ['nullable', 'url'],
            'frequency' => ['nullable', 'integer', 'min:7', 'max:30'], // frequency in days (default 14)
        ];
    }
}
