<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE ORGANIZATION REQUEST (Validasi untuk Tambah Organisasi)
 * ==========================================================================
 */
class StoreOrganizationRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'type'          => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'location'      => 'nullable|string|max:255',
            'website'       => 'nullable|url',
            'contact_email' => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'founded_year'  => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Nama organisasi wajib diisi',
            'name.max'           => 'Nama organisasi maksimal 255 karakter',
            'website.url'        => 'Format URL website tidak valid',
            'contact_email.email'=> 'Format email tidak valid',
            'founded_year.min'   => 'Tahun berdiri tidak boleh sebelum 1800',
            'founded_year.max'   => 'Tahun berdiri tidak boleh di masa depan',
        ];
    }
}
