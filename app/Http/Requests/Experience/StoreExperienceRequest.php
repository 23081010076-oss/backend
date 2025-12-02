<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE EXPERIENCE REQUEST (Validasi untuk Tambah Pengalaman)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user menambah pengalaman baru.
 * 
 * FIELD YANG DIVALIDASI (sesuai database):
 * - type            = Jenis: work/internship/volunteer (wajib)
 * - title           = Jabatan/posisi (wajib)
 * - company         = Nama perusahaan (opsional)
 * - level           = Level (opsional)
 * - start_date      = Tanggal mulai (opsional)
 * - end_date        = Tanggal selesai (opsional, harus >= start_date)
 * - description     = Deskripsi (opsional)
 * - certificate_url = URL sertifikat (opsional)
 */
class StoreExperienceRequest extends FormRequest
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
            'type'            => 'required|in:work,internship,volunteer',
            'title'           => 'required|string|max:255',
            
            // FIELD OPSIONAL
            'company'         => 'nullable|string|max:255',
            'level'           => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'description'     => 'nullable|string',
            'certificate_url' => 'nullable|url|max:255',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'type.required'           => 'Jenis pengalaman wajib dipilih',
            'type.in'                 => 'Jenis harus work, internship, atau volunteer',
            'title.required'          => 'Jabatan/posisi wajib diisi',
            'title.max'               => 'Jabatan maksimal 255 karakter',
            'company.max'             => 'Nama perusahaan maksimal 255 karakter',
            'level.max'               => 'Level maksimal 255 karakter',
            'start_date.date'         => 'Format tanggal mulai tidak valid',
            'end_date.date'           => 'Format tanggal selesai tidak valid',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'certificate_url.url'     => 'Format URL sertifikat tidak valid',
            'certificate_url.max'     => 'URL sertifikat maksimal 255 karakter',
        ];
    }
}
