<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScholarshipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf|max:2048',
            'transcript_path' => 'nullable|file|mimes:pdf|max:2048',
            'recommendation_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'motivation_letter.mimes' => 'File motivation_letter harus PDF atau DOC',
            'motivation_letter.max' => 'File motivation_letter maksimal 2MB',
            'cv_path.mimes' => 'File CV harus PDF',
            'cv_path.max' => 'File CV maksimal 2MB',
        ];
    }
}
