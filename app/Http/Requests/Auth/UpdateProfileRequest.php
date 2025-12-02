<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * ==========================================================================
 * UPDATE PROFILE REQUEST (Validasi untuk Update Profil)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate profil.
 */
class UpdateProfileRequest extends FormRequest
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
        $userId = Auth::id();

        return [
            // DATA DASAR
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            
            // DATA KONTAK & PRIBADI
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'address'    => 'nullable|string|max:500',
            
            // DATA PENDIDIKAN
            'institution'     => 'nullable|string|max:255',
            'major'           => 'nullable|string|max:255',
            'education_level' => 'nullable|in:high_school,diploma,bachelor,master,phd',
            
            // DATA PROFIL
            'bio' => 'nullable|string|max:1000',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'name.max'           => 'Nama maksimal 255 karakter',
            'email.email'        => 'Format email tidak valid',
            'email.unique'       => 'Email sudah digunakan user lain',
            'phone.max'          => 'Nomor telepon maksimal 20 karakter',
            'gender.in'          => 'Gender harus male, female, atau other',
            'birth_date.date'    => 'Format tanggal lahir tidak valid',
            'birth_date.before'  => 'Tanggal lahir harus sebelum hari ini',
            'address.max'        => 'Alamat maksimal 500 karakter',
            'education_level.in' => 'Jenjang pendidikan tidak valid',
            'bio.max'            => 'Bio maksimal 1000 karakter',
        ];
    }
}
