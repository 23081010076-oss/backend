<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurriculumRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration' => 'nullable|string|max:100',
            'section'=> 'nullable|string|max:255',
            'video_url'=> 'nullable|string|max:255',
            'section_order'=> 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Judul kurikulum maksimal 255 karakter',
            'order.integer' => 'Urutan harus berupa angka',
            'order.min' => 'Urutan minimal 0',
            'section.max' => 'Section maksimal 255 karakter',
            'video_url.max' => 'Video URL maksimal 255 karakter',
            'section_order.integer' => 'Section Order harus berupa angka',
            'section_order.min' => 'Section Order minimal 0',
        ];
    }
}
