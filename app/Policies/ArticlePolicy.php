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
 * ATURAN:
 * - Semua orang bisa lihat artikel yang published
 * - Admin bisa lihat semua artikel (termasuk draft)
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
     * → Artikel published: semua boleh
     * → Artikel draft: hanya penulis atau admin
     */
    public function view(?User $user, Article $article): bool
    {
        // Artikel published bisa dilihat semua orang
        if ($article->status === 'published') {
            return true;
        }

        // Artikel draft hanya bisa dilihat penulis atau admin
        if ($user) {
            return $user->id === $article->author_id 
                || $user->role === 'admin';
        }

        return false;
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

    /**
     * Apakah user boleh mempublish artikel?
     * → Admin saja yang boleh
     */
    public function publish(User $user, Article $article): bool
    {
        return $user->role === 'admin';
    }
}
