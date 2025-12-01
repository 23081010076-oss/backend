<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ==========================================================================
 * USER RESOURCE (Format Response untuk User)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur data user yang akan dikirim ke frontend.
 * 
 * CARA PAKAI DI CONTROLLER:
 * 
 * 1. Single User:
 *    return new UserResource($user);
 * 
 * 2. Dengan Pesan:
 *    return $this->successResponse(new UserResource($user), 'Login berhasil');
 * 
 * 3. Banyak User (Collection):
 *    return UserResource::collection($users);
 * 
 * KEUNTUNGAN:
 * - Format response konsisten di semua endpoint
 * - Bisa menyembunyikan field sensitif (contoh: password tidak ditampilkan)
 * - Mudah mengubah format tanggal/data
 * - Controller jadi lebih bersih
 */
class UserResource extends JsonResource
{
    /**
     * Mengubah data user menjadi array untuk response JSON.
     * 
     * $this di sini merujuk ke model User yang di-wrap.
     * Contoh: new UserResource($user) -> $this = $user
     */
    public function toArray(Request $request): array
    {
        return [
            // ============================================================
            // DATA DASAR USER
            // ============================================================
            'id'    => $this->id,           // ID unik user
            'name'  => $this->name,         // Nama lengkap
            'email' => $this->email,        // Alamat email
            'role'  => $this->role,         // Peran: student/mentor/admin/corporate
            
            // ============================================================
            // DATA KONTAK & PRIBADI
            // ============================================================
            'phone'      => $this->phone,                           // Nomor telepon
            'gender'     => $this->gender,                          // Jenis kelamin
            'birth_date' => $this->birth_date?->format('Y-m-d'),    // Tanggal lahir (format: 2000-01-15)
            'address'    => $this->address,                         // Alamat lengkap
            
            // ============================================================
            // DATA PENDIDIKAN
            // ============================================================
            'institution'     => $this->institution,      // Nama institusi/sekolah/kampus
            'major'           => $this->major,            // Jurusan/program studi
            'education_level' => $this->education_level,  // Jenjang: SMA/D3/S1/S2/S3
            
            // ============================================================
            // DATA PROFIL
            // ============================================================
            'bio'           => $this->bio,            // Deskripsi singkat tentang user
            'profile_photo' => $this->profile_photo,  // URL foto profil
            
            // ============================================================
            // TIMESTAMP (Waktu)
            // ============================================================
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),  // Kapan dibuat
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),  // Kapan terakhir diupdate
        ];
    }
}
