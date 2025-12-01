<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * CHANGE PASSWORD REQUEST (Validasi untuk Ganti Password)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user ingin mengganti password.
 */
class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required'         => 'Password baru wajib diisi',
            'password.min'              => 'Password baru minimal 8 karakter',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
        ];
    }
}
