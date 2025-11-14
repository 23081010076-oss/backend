<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScholarshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('corporate');
    }

    public function rules(): array
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'benefit' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:open,coming_soon,closed',
            'deadline' => 'nullable|date|after:today',
            'study_field' => 'nullable|string|max:255',
            'funding_amount' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'organization_id.required' => 'Organisasi wajib dipilih',
            'organization_id.exists' => 'Organisasi tidak ditemukan',
            'name.required' => 'Nama beasiswa wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'deadline.after' => 'Deadline harus di masa depan',
        ];
    }
}
