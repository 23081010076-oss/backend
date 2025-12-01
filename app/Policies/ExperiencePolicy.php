<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Experience;

/**
 * ==========================================================================
 * EXPERIENCE POLICY (Aturan Akses untuk Pengalaman)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap pengalaman.
 * 
 * ATURAN:
 * - Semua user bisa lihat pengalaman (untuk profil publik)
 * - Hanya pemilik yang bisa tambah/edit/hapus pengalamannya sendiri
 * - Admin bisa melihat dan menghapus semua pengalaman
 */
class ExperiencePolicy
{
    /**
     * Apakah user boleh melihat daftar pengalaman?
     * → Semua user yang login boleh
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail pengalaman?
     * → Pemilik atau admin boleh
     */
    public function view(User $user, Experience $experience): bool
    {
        return $user->id === $experience->user_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh membuat pengalaman baru?
     * → Semua user yang login boleh
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh mengupdate pengalaman?
     * → Hanya pemilik yang boleh
     */
    public function update(User $user, Experience $experience): bool
    {
        return $user->id === $experience->user_id;
    }

    /**
     * Apakah user boleh menghapus pengalaman?
     * → Pemilik atau admin boleh
     */
    public function delete(User $user, Experience $experience): bool
    {
        return $user->id === $experience->user_id 
            || $user->role === 'admin';
    }
}
