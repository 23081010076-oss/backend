<?php

namespace App\Http\Requests\Mentoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE MENTORING SESSION REQUEST (Validasi untuk Buat Sesi Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user membuat sesi mentoring baru.
 * 
 * FIELD YANG DIVALIDASI:
 * - mentor_id     = ID mentor (wajib, harus valid)
 * - session_date  = Tanggal & waktu sesi (wajib, harus di masa depan)
 * - duration      = Durasi dalam menit (wajib, 30-180 menit)
 * - topic         = Topik pembahasan (wajib)
 * - notes         = Catatan tambahan (opsional)
 * - meeting_url   = Link meeting (opsional)
 * - session_type  = Jenis: online/offline (opsional)
 * - location      = Lokasi jika offline (opsional)
 */
class StoreMentoringSessionRequest extends FormRequest
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
            'mentor_id'    => 'required|exists:users,id',
            'session_date' => 'required|date|after:now',
            'duration'     => 'required|integer|min:30|max:180',
            'topic'        => 'required|string|max:255',
            
            // FIELD OPSIONAL
            'notes'        => 'nullable|string',
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
            'mentor_id.required'    => 'Mentor wajib dipilih',
            'mentor_id.exists'      => 'Mentor tidak ditemukan',
            'session_date.required' => 'Tanggal sesi wajib diisi',
            'session_date.date'     => 'Format tanggal tidak valid',
            'session_date.after'    => 'Tanggal sesi harus di masa depan',
            'duration.required'     => 'Durasi wajib diisi',
            'duration.integer'      => 'Durasi harus berupa angka',
            'duration.min'          => 'Durasi minimal 30 menit',
            'duration.max'          => 'Durasi maksimal 180 menit (3 jam)',
            'topic.required'        => 'Topik pembahasan wajib diisi',
            'topic.max'             => 'Topik maksimal 255 karakter',
            'meeting_url.url'       => 'Format URL meeting tidak valid',
            'session_type.in'       => 'Jenis sesi harus online atau offline',
            'location.max'          => 'Lokasi maksimal 255 karakter',
        ];
    }
}
