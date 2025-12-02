<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

/**
 * ==========================================================================
 * ARTICLE POLICY (Aturan Akses untuk Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap artikel.
 * 
 * Database columns: id, author_id, title, content, category, author (string), timestamps
 * 
 * ATURAN:
 * - Semua orang bisa lihat artikel
 * - Admin dan mentor bisa membuat artikel
 * - Penulis atau admin yang bisa edit/hapus
 */
class ArticlePolicy
{
    /**
     * Apakah user boleh melihat daftar artikel?
     * → Semua orang boleh
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat artikel tertentu?
     * → Semua artikel bisa dilihat semua orang (no status column in DB)
     */
    public function view(?User $user, Article $article): bool
    {
        return true;
    }

    /**
     * Apakah user boleh membuat artikel?
     * → Admin dan mentor boleh
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'mentor']);
    }

    /**
     * Apakah user boleh mengupdate artikel?
     * → Penulis atau admin boleh
     */
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->author_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh menghapus artikel?
     * → Penulis atau admin boleh
     */
    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->author_id 
            || $user->role === 'admin';
    }
}
