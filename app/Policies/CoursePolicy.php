<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

/**
 * ==========================================================================
 * COURSE POLICY (Aturan Akses untuk Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap kursus.
 * 
 * ATURAN:
 * - Semua orang bisa lihat kursus
 * - Hanya admin yang bisa kelola kursus (CRUD)
 * - User yang terdaftar bisa akses konten kursus berbayar
 */
class CoursePolicy
{
    /**
     * Apakah user boleh melihat daftar kursus?
     * → Semua orang boleh
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail kursus?
     * → Semua orang boleh
     */
    public function view(?User $user, Course $course): bool
    {
        return true;
    }

    /**
     * Apakah user boleh membuat kursus baru?
     * → Hanya admin boleh
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh mengupdate kursus?
     * → Hanya admin boleh
     */
    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh menghapus kursus?
     * → Hanya admin boleh
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }
}
