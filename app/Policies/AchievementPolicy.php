<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Achievement;

/**
 * ==========================================================================
 * ACHIEVEMENT POLICY (Aturan Akses untuk Prestasi)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap prestasi.
 * 
 * ATURAN:
 * - Semua user bisa lihat prestasi
 * - Hanya pemilik yang bisa edit/hapus prestasinya sendiri
 * - Admin bisa melihat dan menghapus semua prestasi
 */
class AchievementPolicy
{
    /**
     * Apakah user boleh melihat daftar prestasi?
     * → Semua user yang login boleh
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail prestasi tertentu?
     * → Pemilik atau admin boleh
     */
    public function view(User $user, Achievement $achievement): bool
    {
        return $user->id === $achievement->user_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh membuat prestasi baru?
     * → Semua user yang login boleh
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh mengupdate prestasi?
     * → Hanya pemilik yang boleh
     */
    public function update(User $user, Achievement $achievement): bool
    {
        return $user->id === $achievement->user_id;
    }

    /**
     * Apakah user boleh menghapus prestasi?
     * → Pemilik atau admin boleh
     */
    public function delete(User $user, Achievement $achievement): bool
    {
        return $user->id === $achievement->user_id 
            || $user->role === 'admin';
    }
}
