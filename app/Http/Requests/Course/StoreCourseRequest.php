<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE COURSE REQUEST (Validasi untuk Tambah Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika admin/mentor menambah kursus baru.
 * 
 * FIELD YANG DIVALIDASI:
 * - title           = Judul kursus (wajib)
 * - description     = Deskripsi kursus (opsional)
 * - type            = Jenis: bootcamp/course (wajib)
 * - level           = Tingkat: beginner/intermediate/advanced (wajib)
 * - duration        = Durasi kursus (opsional)
 * - price           = Harga (opsional, min 0)
 * - access_type     = Tipe akses: free/regular/premium (wajib)
 * - certificate_url = Link sertifikat (opsional)
 * - instructor      = Nama instruktur (opsional)
 * - video_url       = Link video (opsional)
 * - video_duration  = Durasi video (opsional)
 * - total_videos    = Jumlah video (opsional)
 */
class StoreCourseRequest extends FormRequest
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
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'type'        => 'required|in:bootcamp,course',
            'level'       => 'required|in:beginner,intermediate,advanced',
            'access_type' => 'required|in:free,regular,premium',
            
            // FIELD OPSIONAL
            'image'           => 'nullable|jpg,jpeg,png', // Bisa file atau URL, validasi di controller
            'description'     => 'nullable|string',
            'duration'        => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'certificate_url' => 'nullable|string',
            'instructor'      => 'nullable|string',
            'video_url'       => 'nullable|string',
            'video_duration'  => 'nullable|string',
            'total_videos'    => 'nullable|integer|min:0',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Judul kursus wajib diisi',
            'title.max'            => 'Judul maksimal 255 karakter',
            'type.required'        => 'Jenis kursus wajib dipilih',
            'type.in'              => 'Jenis kursus harus bootcamp atau course',
            'level.required'       => 'Tingkat kesulitan wajib dipilih',
            'level.in'             => 'Tingkat harus beginner, intermediate, atau advanced',
            'access_type.required' => 'Tipe akses wajib dipilih',
            'access_type.in'       => 'Tipe akses harus free, regular, atau premium',
            'price.numeric'        => 'Harga harus berupa angka',
            'price.min'            => 'Harga tidak boleh negatif',
            'total_videos.integer' => 'Jumlah video harus berupa angka bulat',
            'total_videos.min'     => 'Jumlah video tidak boleh negatif',
        ];
    }
}
