<?php

namespace App\Http\Requests\Achievement;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE ACHIEVEMENT REQUEST (Validasi untuk Update Prestasi)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate prestasi.
 * 
 * Database columns: id, user_id, title, description, organization, year, timestamps
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 * User hanya perlu kirim field yang ingin diubah.
 */
class UpdateAchievementRequest extends FormRequest
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
            'title'        => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'organization' => 'nullable|string|max:255',
            'year'         => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.max'        => 'Judul maksimal 255 karakter',
            'organization.max' => 'Nama organisasi maksimal 255 karakter',
            'year.integer'     => 'Tahun harus berupa angka',
            'year.min'         => 'Tahun minimal 1900',
            'year.max'         => 'Tahun tidak valid',
        ];
    }
}
