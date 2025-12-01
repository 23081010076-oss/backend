<?php

namespace App\Http\Requests\Coaching;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE COACHING FILE REQUEST (Validasi untuk Upload File Coaching)
 * ==========================================================================
 */
class StoreCoachingFileRequest extends FormRequest
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
            'file_name'   => 'required|string|max:255',
            'file_type'   => 'required|in:pdf,doc,docx,ppt,pptx,video,image,audio',
            'file'        => 'required|file|max:50000', // 50MB max
            'uploaded_by' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'file_name.required'   => 'Nama file wajib diisi',
            'file_name.max'        => 'Nama file maksimal 255 karakter',
            'file_type.required'   => 'Tipe file wajib dipilih',
            'file_type.in'         => 'Tipe file tidak valid',
            'file.required'        => 'File wajib diupload',
            'file.max'             => 'Ukuran file maksimal 50MB',
            'uploaded_by.required' => 'Uploader wajib diisi',
            'uploaded_by.exists'   => 'User tidak ditemukan',
        ];
    }
}
