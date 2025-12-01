<?php

namespace App\Http\Requests\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE ENROLLMENT REQUEST (Validasi untuk Update Enrollment)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika mengupdate enrollment/pendaftaran kursus.
 * 
 * FIELD YANG DIVALIDASI:
 * - progress        = Progress 0-100% (opsional)
 * - completed       = Sudah selesai? (opsional)
 * - certificate_url = Link sertifikat (opsional)
 */
class UpdateEnrollmentRequest extends FormRequest
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
            'progress'        => 'sometimes|integer|min:0|max:100',
            'completed'       => 'sometimes|boolean',
            'certificate_url' => 'nullable|url',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'progress.integer'      => 'Progress harus berupa angka',
            'progress.min'          => 'Progress minimal 0',
            'progress.max'          => 'Progress maksimal 100',
            'completed.boolean'     => 'Status selesai harus true atau false',
            'certificate_url.url'   => 'Format URL sertifikat tidak valid',
        ];
    }
}
