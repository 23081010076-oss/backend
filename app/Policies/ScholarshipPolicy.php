<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Scholarship;

/**
 * ==========================================================================
 * SCHOLARSHIP POLICY (Aturan Akses untuk Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap beasiswa.
 * 
 * ATURAN:
 * - Semua orang bisa lihat beasiswa
 * - Corporate dan admin bisa membuat/update/hapus beasiswa
 * - User bisa melamar beasiswa yang terbuka
 * - Admin bisa update status lamaran
 */
class ScholarshipPolicy
{
    /**
     * Apakah user boleh melihat daftar beasiswa?
     * → Semua orang boleh
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail beasiswa?
     * → Semua orang boleh
     */
    public function view(?User $user, Scholarship $scholarship): bool
    {
        return true;
    }

    /**
     * Apakah user boleh membuat beasiswa baru?
     * → Corporate atau admin boleh
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['corporate', 'admin']);
    }

    /**
     * Apakah user boleh mengupdate beasiswa?
     * → Corporate atau admin boleh
     */
    public function update(User $user, Scholarship $scholarship): bool
    {
        return in_array($user->role, ['corporate', 'admin']);
    }

    /**
     * Apakah user boleh menghapus beasiswa?
     * → Corporate atau admin boleh
     */
    public function delete(User $user, Scholarship $scholarship): bool
    {
        return in_array($user->role, ['corporate', 'admin']);
    }

    /**
     * Apakah user boleh melamar beasiswa?
     * → Semua user yang login boleh (jika beasiswa terbuka)
     */
    public function apply(User $user, Scholarship $scholarship): bool
    {
        return $scholarship->status === 'open';
    }

    /**
     * Apakah user boleh mengupdate status lamaran?
     * → Hanya admin boleh
     */
    public function updateApplicationStatus(User $user, Scholarship $scholarship): bool
    {
        return $user->role === 'admin';
    }
}
