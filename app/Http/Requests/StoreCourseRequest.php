<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:regular,bootcamp',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'access_type' => 'required|in:free,regular,premium',
            'certificate_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'video_duration' => 'nullable|string|max:100',
            'total_videos' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kursus wajib diisi',
            'title.unique' => 'Judul kursus sudah ada',
            'description.required' => 'Deskripsi kursus wajib diisi',
            'type.required' => 'Tipe kursus wajib dipilih',
            'level.required' => 'Level kursus wajib dipilih',
            'access_type.required' => 'Tipe akses wajib dipilih',
        ];
    }
}
