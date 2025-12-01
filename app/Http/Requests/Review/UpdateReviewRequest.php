<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE REVIEW REQUEST (Validasi untuk Update Review)
 * ==========================================================================
 */
class UpdateReviewRequest extends FormRequest
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
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|min:3|max:1000',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'rating.min'  => 'Rating minimal 1 bintang',
            'rating.max'  => 'Rating maksimal 5 bintang',
            'comment.min' => 'Komentar minimal 3 karakter',
            'comment.max' => 'Komentar maksimal 1000 karakter',
        ];
    }
}
