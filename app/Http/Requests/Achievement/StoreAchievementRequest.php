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
 * FIELD YANG DIVALIDASI:
 * - title       = Judul prestasi (wajib)
 * - description = Deskripsi prestasi (opsional)
 * - type        = Jenis: certificate/award/publication/project/other (wajib)
 * - issuer      = Penerbit/penyelenggara (opsional)
 * - date        = Tanggal pencapaian (wajib)
 * - url         = Link bukti (opsional)
 * - image_url   = Link gambar (opsional)
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
            'type'  => 'required|in:certificate,award,publication,project,other',
            'date'  => 'required|date',
            
            // FIELD OPSIONAL
            'description' => 'nullable|string',
            'issuer'      => 'nullable|string|max:255',
            'url'         => 'nullable|url',
            'image_url'   => 'nullable|url',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul prestasi wajib diisi',
            'title.max'      => 'Judul maksimal 255 karakter',
            'type.required'  => 'Jenis prestasi wajib dipilih',
            'type.in'        => 'Jenis prestasi tidak valid (pilih: certificate/award/publication/project/other)',
            'date.required'  => 'Tanggal prestasi wajib diisi',
            'date.date'      => 'Format tanggal tidak valid',
            'issuer.max'     => 'Nama penerbit maksimal 255 karakter',
            'url.url'        => 'Format URL tidak valid',
            'image_url.url'  => 'Format URL gambar tidak valid',
        ];
    }
}
