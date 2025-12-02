<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * ==========================================================================
 * UPDATE PROFILE REQUEST (Validasi untuk Update Profil)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate profil.
 * 
 * CATATAN PENTING:
 * - Semua field OPSIONAL (pakai 'sometimes' atau 'nullable')
 * - User hanya perlu kirim field yang ingin diubah
 * - Email harus unik (kecuali email milik user sendiri)
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
     * 
     * Penjelasan aturan khusus:
     * - sometimes  = hanya validasi jika field ada di request
     * - nullable   = boleh kosong/null
     * - unique:users,email,{userId} = email harus unik, KECUALI milik user ini
     */
    public function rules(): array
    {
        // Ambil ID user yang sedang login
        // Ini untuk pengecualian validasi unique email
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
            // Pesan untuk NAME
            'name.string' => 'Nama harus berupa teks',
            'name.max'    => 'Nama maksimal 255 karakter',
            
            // Pesan untuk EMAIL
            'email.email'  => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan user lain',
            
            // Pesan untuk PHONE
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            
            // Pesan untuk GENDER
            'gender.in' => 'Gender harus male, female, atau other',
            
            // Pesan untuk BIRTH_DATE
            'birth_date.date'   => 'Format tanggal lahir tidak valid',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini',
            
            // Pesan untuk ADDRESS
            'address.max' => 'Alamat maksimal 500 karakter',
            
            // Pesan untuk EDUCATION_LEVEL
            'education_level.in' => 'Jenjang pendidikan tidak valid (pilih: high_school/diploma/bachelor/master/phd)',
            
            // Pesan untuk BIO
            'bio.max' => 'Bio maksimal 1000 karakter',
        ];
    }
}
