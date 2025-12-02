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
 * FIELD YANG DIVALIDASI (sesuai database):
 * - mentor_id     = ID mentor (wajib, harus valid)
 * - type          = Jenis: academic/life_plan (wajib)
 * - schedule      = Tanggal & waktu sesi (opsional)
 * - meeting_link  = Link meeting (opsional)
 * - payment_method = Metode pembayaran (opsional)
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
            'mentor_id'      => 'required|exists:users,id',
            'type'           => 'required|in:academic,life_plan',
            
            // FIELD OPSIONAL
            'schedule'       => 'nullable|date|after:now',
            'meeting_link'   => 'nullable|url',
            'payment_method' => 'nullable|in:qris,bank,va,manual',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'mentor_id.required'  => 'Mentor wajib dipilih',
            'mentor_id.exists'    => 'Mentor tidak ditemukan',
            'type.required'       => 'Jenis sesi wajib dipilih',
            'type.in'             => 'Jenis sesi harus academic atau life_plan',
            'schedule.date'       => 'Format tanggal tidak valid',
            'schedule.after'      => 'Jadwal sesi harus di masa depan',
            'meeting_link.url'    => 'Format URL meeting tidak valid',
            'payment_method.in'   => 'Metode pembayaran tidak valid',
        ];
    }
}
