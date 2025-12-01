<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE USER REQUEST (Validasi untuk Tambah User - Admin Only)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika admin menambah user baru.
 * 
 * FIELD YANG DIVALIDASI:
 * - name         = Nama lengkap (wajib)
 * - email        = Email (wajib, harus unik)
 * - password     = Password (wajib, min 8 karakter)
 * - role         = Peran: admin/student/mentor/corporate (wajib)
 * - phone        = Nomor telepon (opsional)
 * - bio          = Biografi (opsional)
 * - status       = Status: active/inactive/suspended (opsional)
 * - organization = Nama organisasi (opsional)
 * - job_title    = Jabatan (opsional)
 */
class StoreUserRequest extends FormRequest
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
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,student,mentor,corporate',
            
            // FIELD OPSIONAL
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
            'name.required'     => 'Nama wajib diisi',
            'name.max'          => 'Nama maksimal 255 karakter',
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'role.required'     => 'Role wajib dipilih',
            'role.in'           => 'Role harus admin, student, mentor, atau corporate',
            'phone.max'         => 'Nomor telepon maksimal 20 karakter',
            'status.in'         => 'Status harus active, inactive, atau suspended',
            'organization.max'  => 'Nama organisasi maksimal 255 karakter',
            'job_title.max'     => 'Jabatan maksimal 255 karakter',
        ];
    }
}
