<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'institution' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'education_level' => 'nullable|in:high_school,diploma,bachelor,master,phd',
            'bio' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nama harus berupa teks',
            'email.unique' => 'Email sudah terdaftar',
            'email.email' => 'Format email tidak valid',
            'birth_date.before' => 'Tanggal lahir harus di masa lalu',
        ];
    }
}
