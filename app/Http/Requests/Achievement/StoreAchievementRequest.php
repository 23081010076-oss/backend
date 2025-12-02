<?php

namespace App\Http\Requests\Achievement;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE ACHIEVEMENT REQUEST (Validasi untuk Tambah Prestasi)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user menambah prestasi baru.
 * 
 * Database columns: id, user_id, title, description, organization, year, timestamps
 * 
 * FIELD YANG DIVALIDASI:
 * - title        = Judul prestasi (wajib)
 * - description  = Deskripsi prestasi (opsional)
 * - organization = Organisasi/penyelenggara (opsional)
 * - year         = Tahun pencapaian (opsional)
 */
class StoreAchievementRequest extends FormRequest
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
            // FIELD WAJIB
            'title' => 'required|string|max:255',
            
            // FIELD OPSIONAL
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
            'title.required'   => 'Judul prestasi wajib diisi',
            'title.max'        => 'Judul maksimal 255 karakter',
            'organization.max' => 'Nama organisasi maksimal 255 karakter',
            'year.integer'     => 'Tahun harus berupa angka',
            'year.min'         => 'Tahun minimal 1900',
            'year.max'         => 'Tahun tidak valid',
        ];
    }
}
