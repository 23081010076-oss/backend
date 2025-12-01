<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE NEED ASSESSMENT REQUEST (Validasi untuk Tambah Need Assessment)
 * ==========================================================================
 */
class StoreNeedAssessmentRequest extends FormRequest
{
    /**
     * Apakah user boleh akses endpoint ini?
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ATURAN VALIDASI
     */
    public function rules(): array
    {
        return [
            'form_data'                      => 'required|array',
            'form_data.learning_goals'       => 'required|string|max:500',
            'form_data.previous_experience'  => 'required|string|max:500',
            'form_data.challenges'           => 'required|string|max:500',
            'form_data.expectations'         => 'required|string|max:500',
            'completed_at'                   => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'form_data.required'                     => 'Data form wajib diisi',
            'form_data.learning_goals.required'      => 'Tujuan belajar wajib diisi',
            'form_data.learning_goals.max'           => 'Tujuan belajar maksimal 500 karakter',
            'form_data.previous_experience.required' => 'Pengalaman sebelumnya wajib diisi',
            'form_data.previous_experience.max'      => 'Pengalaman sebelumnya maksimal 500 karakter',
            'form_data.challenges.required'          => 'Tantangan wajib diisi',
            'form_data.challenges.max'               => 'Tantangan maksimal 500 karakter',
            'form_data.expectations.required'        => 'Ekspektasi wajib diisi',
            'form_data.expectations.max'             => 'Ekspektasi maksimal 500 karakter',
        ];
    }
}
