<?php

namespace App\Http\Requests\Mentoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE MENTORING SESSION REQUEST (Validasi untuk Update Sesi Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user/mentor mengupdate sesi mentoring.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateMentoringSessionRequest extends FormRequest
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
            'session_date' => 'sometimes|date|after:now',
            'duration'     => 'sometimes|integer|min:30|max:180',
            'topic'        => 'sometimes|string|max:255',
            'notes'        => 'nullable|string',
            'status'       => 'sometimes|in:pending,confirmed,completed,cancelled',
            'meeting_url'  => 'nullable|url',
            'session_type' => 'nullable|in:online,offline',
            'location'     => 'nullable|string|max:255',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'session_date.date'  => 'Format tanggal tidak valid',
            'session_date.after' => 'Tanggal sesi harus di masa depan',
            'duration.integer'   => 'Durasi harus berupa angka',
            'duration.min'       => 'Durasi minimal 30 menit',
            'duration.max'       => 'Durasi maksimal 180 menit (3 jam)',
            'topic.max'          => 'Topik maksimal 255 karakter',
            'status.in'          => 'Status harus pending, confirmed, completed, atau cancelled',
            'meeting_url.url'    => 'Format URL meeting tidak valid',
            'session_type.in'    => 'Jenis sesi harus online atau offline',
            'location.max'       => 'Lokasi maksimal 255 karakter',
        ];
    }
}
