<?php

namespace App\Http\Requests\Scholarship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE SCHOLARSHIP REQUEST (Validasi untuk Tambah Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika corporate/admin menambah beasiswa baru.
 * 
 * FIELD YANG DIVALIDASI:
 * - name           = Nama beasiswa (wajib)
 * - description    = Deskripsi (opsional)
 * - benefit        = Keuntungan/fasilitas (opsional)
 * - location       = Lokasi studi (opsional)
 * - status         = Status: open/coming_soon/closed (wajib)
 * - deadline       = Batas waktu pendaftaran (opsional)
 * - study_field    = Bidang studi (opsional)
 * - funding_amount = Jumlah dana (opsional)
 * - requirements   = Persyaratan (opsional)
 */
class StoreScholarshipRequest extends FormRequest
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
            'name'   => 'required|string|max:255',
            'status' => 'required|in:open,coming_soon,closed',
            
            // FIELD OPSIONAL
            'organization_id' => 'nullable|exists:organizations,id',
            'provider_id'     => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'benefit'         => 'nullable|string',
            'location'        => 'nullable|string',
            'deadline'        => 'nullable|date',
            'study_field'     => 'nullable|string|max:255',
            'funding_amount'  => 'nullable|numeric|min:0',
            'requirements'    => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.required'          => 'Nama beasiswa wajib diisi',
            'name.max'               => 'Nama beasiswa maksimal 255 karakter',
            'status.required'        => 'Status beasiswa wajib dipilih',
            'status.in'              => 'Status harus open, coming_soon, atau closed',
            'organization_id.exists' => 'Organisasi tidak ditemukan',
            'deadline.date'          => 'Format deadline tidak valid',
            'study_field.max'        => 'Bidang studi maksimal 255 karakter',
            'funding_amount.numeric' => 'Jumlah dana harus berupa angka',
            'funding_amount.min'     => 'Jumlah dana tidak boleh negatif',
        ];
    }
}
