<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * REGISTER REQUEST (Validasi untuk Pendaftaran)
 * ==========================================================================
 *
 * FUNGSI: Memvalidasi data pendaftaran user baru.
 */
class RegisterRequest extends FormRequest
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
            // FIELD WAJIB
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

            // FIELD OPSIONAL
            'role'     => 'nullable|in:student,mentor,admin,corporate',
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi',
            'name.max'           => 'Nama maksimal 255 karakter',
            'email.required'     => 'Email wajib diisi',
            'email.email'        => 'Format email tidak valid',
            'email.unique'       => 'Email sudah terdaftar',
            'password.required'  => 'Password wajib diisi',
            'password.min'       => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.in'            => 'Role tidak valid (pilih: student/mentor/admin/corporate)',
            'gender.in'          => 'Gender tidak valid (pilih: male/female/other)',
            'birth_date.date'    => 'Format tanggal lahir tidak valid',
            'birth_date.before'  => 'Tanggal lahir harus sebelum hari ini',
        ];
    }
}
