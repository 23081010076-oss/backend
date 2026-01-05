<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurriculumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration' => 'nullable|string|max:100',
            
            // Section (optional, auto-generated if not provided)
            'section' => 'nullable|string|max:50',
            
            // Parent section untuk nested structure (optional)
            // Format: "1" untuk root, "1.1" untuk sub, "1.1.1" untuk sub-sub
            'parent_section' => 'nullable|string|max:50',
            
            'video_url' => 'nullable|url|max:500',
            'section_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kurikulum wajib diisi',
            'title.max' => 'Judul kurikulum maksimal 255 karakter',
            'order.integer' => 'Urutan harus berupa angka',
            'order.min' => 'Urutan minimal 0',
            'section.max' => 'Section maksimal 50 karakter',
            'parent_section.max' => 'Parent section maksimal 50 karakter',
            'video_url.url' => 'Video URL harus berupa URL yang valid',
            'video_url.max' => 'Video URL maksimal 500 karakter',
            'section_order.integer' => 'Section Order harus berupa angka',
            'section_order.min' => 'Section Order minimal 0',
        ];
    }
}
