<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE SUBSCRIPTION REQUEST (Validasi untuk Tambah Langganan)
 * ==========================================================================
 */
class StoreSubscriptionRequest extends FormRequest
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
            'plan'          => 'required|in:free,regular,premium',
            'package_type'  => 'required|in:single_course,all_in_one',
            'courses_ids'   => 'nullable|array',
            'courses_ids.*' => 'exists:courses,id',
            'duration'      => 'required|integer|min:1',
            'duration_unit' => 'required|in:months,years',
            'price'         => 'required|numeric|min:0',
            'auto_renew'    => 'boolean',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'plan.required'        => 'Paket langganan wajib dipilih',
            'plan.in'              => 'Paket langganan tidak valid',
            'package_type.required'=> 'Tipe paket wajib dipilih',
            'courses_ids.*.exists' => 'Satu atau lebih kursus tidak ditemukan',
            'duration.required'    => 'Durasi wajib diisi',
            'duration.min'         => 'Durasi minimal 1',
            'price.required'       => 'Harga wajib diisi',
            'price.min'            => 'Harga tidak boleh negatif',
            'start_date.required'  => 'Tanggal mulai wajib diisi',
            'end_date.after'       => 'Tanggal berakhir harus setelah tanggal mulai',
        ];
    }

    /**
     * Persiapan data sebelum validasi
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('auto_renew')) {
            $this->merge(['auto_renew' => false]);
        }
    }
}
