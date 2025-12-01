<?php

namespace App\Policies;

use App\Models\User;

/**
 * ==========================================================================
 * USER POLICY (Aturan Akses untuk Manajemen User)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap user.
 * 
 * ATURAN:
 * - Hanya admin yang bisa kelola user
 * - Admin tidak bisa hapus/suspend diri sendiri
 */
class UserPolicy
{
    /**
     * Apakah user boleh melihat daftar user?
     * → Hanya admin boleh
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh melihat detail user tertentu?
     * → Admin boleh lihat semua
     * → User boleh lihat profilnya sendiri
     */
    public function view(User $user, User $targetUser): bool
    {
        return $user->role === 'admin' 
            || $user->id === $targetUser->id;
    }

    /**
     * Apakah user boleh membuat user baru?
     * → Hanya admin boleh
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh mengupdate user?
     * → Admin boleh update semua
     * → User boleh update profilnya sendiri
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->role === 'admin' 
            || $user->id === $targetUser->id;
    }

    /**
     * Apakah user boleh menghapus user?
     * → Hanya admin boleh
     * → Admin tidak bisa hapus diri sendiri
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->role === 'admin' 
            && $user->id !== $targetUser->id;
    }

    /**
     * Apakah user boleh suspend user lain?
     * → Hanya admin boleh
     * → Admin tidak bisa suspend diri sendiri
     */
    public function suspend(User $user, User $targetUser): bool
    {
        return $user->role === 'admin' 
            && $user->id !== $targetUser->id;
    }

    /**
     * Apakah user boleh mengaktifkan user?
     * → Hanya admin boleh
     */
    public function activate(User $user, User $targetUser): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh melihat statistik?
     * → Hanya admin boleh
     */
    public function viewStatistics(User $user): bool
    {
        return $user->role === 'admin';
    }
}
