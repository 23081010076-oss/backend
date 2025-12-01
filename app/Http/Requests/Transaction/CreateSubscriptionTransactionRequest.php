<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * CREATE SUBSCRIPTION TRANSACTION REQUEST (Validasi Transaksi Langganan)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user berlangganan.
 * 
 * FIELD YANG DIVALIDASI:
 * - plan           = Paket langganan: regular/premium (wajib)
 * - payment_method = Metode pembayaran (wajib)
 */
class CreateSubscriptionTransactionRequest extends FormRequest
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
            'plan'           => 'required|in:regular,premium',
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'plan.required'           => 'Paket langganan wajib dipilih',
            'plan.in'                 => 'Paket harus regular atau premium',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'payment_method.in'       => 'Metode pembayaran tidak valid',
        ];
    }
}
