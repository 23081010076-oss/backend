<?php

namespace App\Http\Requests\Review;

use App\Models\Course;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * STORE REVIEW REQUEST (Validasi untuk Tambah Review)
 * ==========================================================================
 */
class StoreReviewRequest extends FormRequest
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
        $allowedTypes = [
            Course::class,
            User::class,
            Organization::class,
        ];

        return [
            'reviewable_id'   => 'required|integer',
            'reviewable_type' => ['required', 'string', Rule::in($allowedTypes)],
            'rating'          => 'required|integer|min:1|max:5',
            'comment'         => 'required|string|min:3|max:1000',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'reviewable_id.required'   => 'Item yang direview wajib dipilih',
            'reviewable_type.required' => 'Tipe review wajib diisi',
            'reviewable_type.in'       => 'Tipe review tidak valid',
            'rating.required'          => 'Rating wajib diisi',
            'rating.min'               => 'Rating minimal 1 bintang',
            'rating.max'               => 'Rating maksimal 5 bintang',
            'comment.required'         => 'Komentar wajib diisi',
            'comment.min'              => 'Komentar minimal 3 karakter',
            'comment.max'              => 'Komentar maksimal 1000 karakter',
        ];
    }

    /**
     * Validasi tambahan
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reviewableClass = $this->input('reviewable_type');
            
            if (class_exists($reviewableClass)) {
                $reviewableItem = $reviewableClass::find($this->input('reviewable_id'));
                
                if (!$reviewableItem) {
                    $validator->errors()->add(
                        'reviewable_id',
                        'Item yang direview tidak ditemukan'
                    );
                }
            }
        });
    }
}
