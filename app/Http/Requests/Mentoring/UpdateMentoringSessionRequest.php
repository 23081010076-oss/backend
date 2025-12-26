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
            'schedule'       => 'sometimes|date|after:now',
            'meeting_link'   => 'nullable|url',
            'payment_method' => 'nullable|in:manual,bank_transfer',
            'status'         => 'sometimes|in:pending,completed,refunded,scheduled,cancelled',
            'type'           => 'sometimes|in:academic,life_plan',
            'notes'          => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'schedule.date'       => 'Format tanggal tidak valid',
            'schedule.after'      => 'Jadwal sesi harus di masa depan',
            'meeting_link.url'    => 'Format URL meeting tidak valid',
            'payment_method.in'   => 'Metode pembayaran tidak valid (manual atau bank_transfer)',
            'status.in'           => 'Status harus pending, completed, refunded, scheduled, atau cancelled',
            'type.in'             => 'Jenis sesi harus academic atau life_plan',
        ];
    }
}
