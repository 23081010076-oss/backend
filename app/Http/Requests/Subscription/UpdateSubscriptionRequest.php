<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE SUBSCRIPTION REQUEST (Validasi untuk Update Langganan)
 * ==========================================================================
 */
class UpdateSubscriptionRequest extends FormRequest
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
            'plan'       => 'sometimes|in:free,regular,premium',
            'start_date' => 'sometimes|date',
            'end_date'   => 'nullable|date|after:start_date',
            'status'     => 'sometimes|in:active,expired,cancelled',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'plan.in'        => 'Paket langganan tidak valid',
            'start_date.date'=> 'Format tanggal mulai tidak valid',
            'end_date.date'  => 'Format tanggal berakhir tidak valid',
            'end_date.after' => 'Tanggal berakhir harus setelah tanggal mulai',
            'status.in'      => 'Status tidak valid',
        ];
    }
}
