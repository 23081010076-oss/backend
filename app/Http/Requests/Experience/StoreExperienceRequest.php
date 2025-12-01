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
 * FIELD YANG DIVALIDASI:
 * - type         = Jenis: work/education/volunteer/internship (wajib)
 * - title        = Jabatan/posisi (wajib)
 * - organization = Nama organisasi/perusahaan (wajib)
 * - location     = Lokasi (opsional)
 * - start_date   = Tanggal mulai (wajib)
 * - end_date     = Tanggal selesai (opsional, harus >= start_date)
 * - is_current   = Masih aktif? (opsional)
 * - description  = Deskripsi (opsional)
 * - skills       = Skill yang digunakan (opsional)
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
            'type'         => 'required|in:work,education,volunteer,internship',
            'title'        => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date'   => 'required|date',
            
            // FIELD OPSIONAL
            'location'    => 'nullable|string|max:255',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_current'  => 'nullable|boolean',
            'description' => 'nullable|string',
            'skills'      => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'type.required'         => 'Jenis pengalaman wajib dipilih',
            'type.in'               => 'Jenis harus work, education, volunteer, atau internship',
            'title.required'        => 'Jabatan/posisi wajib diisi',
            'title.max'             => 'Jabatan maksimal 255 karakter',
            'organization.required' => 'Nama organisasi wajib diisi',
            'organization.max'      => 'Nama organisasi maksimal 255 karakter',
            'start_date.required'   => 'Tanggal mulai wajib diisi',
            'start_date.date'       => 'Format tanggal mulai tidak valid',
            'location.max'          => 'Lokasi maksimal 255 karakter',
            'end_date.date'         => 'Format tanggal selesai tidak valid',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'is_current.boolean'    => 'Status aktif harus true atau false',
        ];
    }
}
