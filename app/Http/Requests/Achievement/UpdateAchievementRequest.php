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
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'sometimes|in:certificate,award,publication,project,other',
            'issuer'      => 'nullable|string|max:255',
            'date'        => 'sometimes|date',
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
            'title.max'     => 'Judul maksimal 255 karakter',
            'type.in'       => 'Jenis prestasi tidak valid (pilih: certificate/award/publication/project/other)',
            'date.date'     => 'Format tanggal tidak valid',
            'issuer.max'    => 'Nama penerbit maksimal 255 karakter',
            'url.url'       => 'Format URL tidak valid',
            'image_url.url' => 'Format URL gambar tidak valid',
        ];
    }
}
