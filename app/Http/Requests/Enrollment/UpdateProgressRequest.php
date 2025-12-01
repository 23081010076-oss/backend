<?php

namespace App\Http\Requests\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE PROGRESS REQUEST (Validasi untuk Update Progress Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate progress kursus.
 * 
 * FIELD YANG DIVALIDASI:
 * - progress = Progress 0-100% (wajib)
 */
class UpdateProgressRequest extends FormRequest
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
            'progress' => 'required|integer|min:0|max:100',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'progress.required' => 'Progress wajib diisi',
            'progress.integer'  => 'Progress harus berupa angka',
            'progress.min'      => 'Progress minimal 0',
            'progress.max'      => 'Progress maksimal 100',
        ];
    }
}
