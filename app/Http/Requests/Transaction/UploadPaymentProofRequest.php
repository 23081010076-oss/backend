<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPLOAD PAYMENT PROOF REQUEST (Validasi Upload Bukti Pembayaran)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi file bukti pembayaran manual.
 * 
 * FIELD YANG DIVALIDASI:
 * - payment_proof = File gambar/pdf bukti pembayaran (wajib, max 5MB)
 */
class UploadPaymentProofRequest extends FormRequest
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
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'payment_proof.required' => 'Bukti pembayaran wajib diupload',
            'payment_proof.image'    => 'File harus berupa gambar',
            'payment_proof.mimes'    => 'Format file harus jpeg, png, jpg, atau pdf',
            'payment_proof.max'      => 'Ukuran file maksimal 5MB',
        ];
    }
}
