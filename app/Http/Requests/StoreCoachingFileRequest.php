<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoachingFileRequest extends FormRequest
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
            'file_name' => ['required', 'string', 'max:255'],
            'file_type' => ['required', 'in:pdf,doc,docx,ppt,pptx,video,image,audio'],
            'file' => ['required', 'file', 'max:50000'], // 50MB max
            'uploaded_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
