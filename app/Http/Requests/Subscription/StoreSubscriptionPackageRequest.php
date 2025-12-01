<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE SUBSCRIPTION PACKAGE REQUEST (Validasi untuk Tambah Paket Langganan)
 * ==========================================================================
 */
class StoreSubscriptionPackageRequest extends FormRequest
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
            'package_type'  => 'required|in:single_course,all_in_one',
            'duration'      => 'required|integer|in:1,3,12',
            'duration_unit' => 'required|in:months,years',
            'courses_ids'   => 'required_if:package_type,single_course|array',
            'courses_ids.*' => 'integer|exists:courses,id',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'package_type.required'     => 'Tipe paket wajib dipilih',
            'package_type.in'           => 'Tipe paket tidak valid',
            'duration.required'         => 'Durasi wajib dipilih',
            'duration.in'               => 'Durasi harus 1, 3, atau 12',
            'duration_unit.required'    => 'Unit durasi wajib dipilih',
            'courses_ids.required_if'   => 'Kursus wajib dipilih untuk paket single course',
            'courses_ids.*.exists'      => 'Kursus tidak ditemukan',
        ];
    }
}
