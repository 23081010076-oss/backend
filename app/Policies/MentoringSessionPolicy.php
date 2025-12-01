<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MentoringSession;

/**
 * ==========================================================================
 * MENTORING SESSION POLICY (Aturan Akses untuk Sesi Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap sesi mentoring.
 * 
 * ATURAN:
 * - Student bisa membuat sesi dan melihat sesinya sendiri
 * - Mentor bisa melihat dan mengelola sesi yang ditugaskan
 * - Admin bisa melihat dan mengelola semua sesi
 */
class MentoringSessionPolicy
{
    /**
     * Apakah user boleh melihat daftar sesi?
     * → Semua user yang login boleh
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail sesi?
     * → Peserta, mentor yang ditugaskan, atau admin boleh
     */
    public function view(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->user_id 
            || $user->id === $session->mentor_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh membuat sesi baru?
     * → Semua user yang login boleh
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh mengupdate sesi?
     * → Peserta, mentor yang ditugaskan, atau admin boleh
     */
    public function update(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->user_id 
            || $user->id === $session->mentor_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh menghapus sesi?
     * → Peserta atau admin boleh
     */
    public function delete(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->user_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh mengubah status sesi?
     * → Hanya mentor yang ditugaskan atau admin boleh
     */
    public function updateStatus(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->mentor_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh memberikan feedback?
     * → Peserta atau mentor yang terlibat dalam sesi boleh
     */
    public function giveFeedback(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->user_id 
            || $user->id === $session->mentor_id;
    }
}
