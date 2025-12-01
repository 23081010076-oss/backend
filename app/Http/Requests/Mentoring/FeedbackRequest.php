<?php

namespace App\Http\Requests\Mentoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * FEEDBACK REQUEST (Validasi untuk Feedback Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data feedback setelah sesi mentoring selesai.
 * 
 * FIELD YANG DIVALIDASI:
 * - rating   = Rating 1-5 (wajib)
 * - feedback = Komentar/ulasan (opsional)
 */
class FeedbackRequest extends FormRequest
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
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Rating wajib diisi',
            'rating.integer'  => 'Rating harus berupa angka',
            'rating.min'      => 'Rating minimal 1',
            'rating.max'      => 'Rating maksimal 5',
        ];
    }
}
