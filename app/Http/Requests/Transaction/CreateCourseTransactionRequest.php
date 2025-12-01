<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * CREATE COURSE TRANSACTION REQUEST (Validasi Transaksi Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user membeli kursus.
 * 
 * FIELD YANG DIVALIDASI:
 * - payment_method = Metode pembayaran (wajib)
 */
class CreateCourseTransactionRequest extends FormRequest
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
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'payment_method.in'       => 'Metode pembayaran tidak valid (pilih: qris/bank_transfer/virtual_account/credit_card/manual)',
        ];
    }
}
