<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE COURSE REQUEST (Validasi untuk Update Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika admin/mentor mengupdate kursus.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateCourseRequest extends FormRequest
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
            'title'           => 'sometimes|string|max:255',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'type'            => 'sometimes|in:bootcamp,course',
            'level'           => 'sometimes|in:beginner,intermediate,advanced',
            'duration'        => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'access_type'     => 'sometimes|in:free,regular,premium',
            'certificate_url' => 'nullable|string',
            'video_file'      => 'nullable|file|mimes:mp4,avi,mov,mkv,flv|max:524288',
            'video_url'       => 'nullable|string|url',
            'video_duration'  => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.max'        => 'Judul maksimal 255 karakter',
            'type.in'          => 'Jenis kursus harus bootcamp atau course',
            'level.in'         => 'Tingkat harus beginner, intermediate, atau advanced',
            'access_type.in'   => 'Tipe akses harus free, regular, atau premium',
            'price.numeric'    => 'Harga harus berupa angka',
            'price.min'        => 'Harga tidak boleh negatif',
            'video_file.file'  => 'Video harus berupa file',
            'video_file.mimes' => 'Format video harus mp4, avi, mov, mkv, atau flv',
            'video_file.max'   => 'Ukuran video maksimal 512MB',
            'video_url.url'    => 'Format URL video tidak valid',
        ];
    }
}
