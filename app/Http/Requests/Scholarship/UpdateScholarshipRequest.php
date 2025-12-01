<?php

namespace App\Http\Requests\Scholarship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE SCHOLARSHIP REQUEST (Validasi untuk Update Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika corporate/admin mengupdate beasiswa.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateScholarshipRequest extends FormRequest
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
            'organization_id' => 'sometimes|nullable|exists:organizations,id',
            'provider_id'     => 'nullable|string',
            'name'            => 'sometimes|string|max:255',
            'description'     => 'nullable|string',
            'benefit'         => 'nullable|string',
            'location'        => 'nullable|string',
            'status'          => 'sometimes|in:open,coming_soon,closed',
            'deadline'        => 'nullable|date',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.max'               => 'Nama beasiswa maksimal 255 karakter',
            'status.in'              => 'Status harus open, coming_soon, atau closed',
            'organization_id.exists' => 'Organisasi tidak ditemukan',
            'deadline.date'          => 'Format deadline tidak valid',
        ];
    }
}
