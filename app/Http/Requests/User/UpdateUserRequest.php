<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE USER REQUEST (Validasi untuk Update User - Admin Only)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika admin mengupdate user.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateUserRequest extends FormRequest
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
        // Ambil ID user dari route parameter
        $userId = $this->route('id');
        
        return [
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:users,email,' . $userId,
            'password'     => 'sometimes|string|min:8',
            'role'         => 'sometimes|in:admin,student,mentor,corporate',
            'phone'        => 'nullable|string|max:20',
            'bio'          => 'nullable|string',
            'status'       => 'nullable|in:active,inactive,suspended',
            'organization' => 'nullable|string|max:255',
            'job_title'    => 'nullable|string|max:255',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.max'         => 'Nama maksimal 255 karakter',
            'email.email'      => 'Format email tidak valid',
            'email.unique'     => 'Email sudah digunakan user lain',
            'password.min'     => 'Password minimal 8 karakter',
            'role.in'          => 'Role harus admin, student, mentor, atau corporate',
            'phone.max'        => 'Nomor telepon maksimal 20 karakter',
            'status.in'        => 'Status harus active, inactive, atau suspended',
            'organization.max' => 'Nama organisasi maksimal 255 karakter',
            'job_title.max'    => 'Jabatan maksimal 255 karakter',
        ];
    }
}
