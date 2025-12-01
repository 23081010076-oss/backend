<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE EXPERIENCE REQUEST (Validasi untuk Update Pengalaman)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate pengalaman.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateExperienceRequest extends FormRequest
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
            'type'         => 'sometimes|in:work,education,volunteer,internship',
            'title'        => 'sometimes|string|max:255',
            'organization' => 'sometimes|string|max:255',
            'location'     => 'nullable|string|max:255',
            'start_date'   => 'sometimes|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'is_current'   => 'nullable|boolean',
            'description'  => 'nullable|string',
            'skills'       => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'type.in'               => 'Jenis harus work, education, volunteer, atau internship',
            'title.max'             => 'Jabatan maksimal 255 karakter',
            'organization.max'      => 'Nama organisasi maksimal 255 karakter',
            'start_date.date'       => 'Format tanggal mulai tidak valid',
            'location.max'          => 'Lokasi maksimal 255 karakter',
            'end_date.date'         => 'Format tanggal selesai tidak valid',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'is_current.boolean'    => 'Status aktif harus true atau false',
        ];
    }
}
