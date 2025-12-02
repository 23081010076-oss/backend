<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * ==========================================================================
 * ARTICLE SERVICE (Service untuk Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen artikel.
 * 
 * Database columns: id, author_id, title, content, category, author (string), timestamps
 */
class ArticleService
{
    /**
     * Ambil daftar artikel dengan filter
     */
    public function getArticles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::with('authorUser');

        // Filter berdasarkan kategori
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Pencarian berdasarkan judul atau konten
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Buat artikel baru
     */
    public function createArticle(array $data, User $author, $imageFile = null): Article
    {
        $data['author_id'] = $author->id;
        
        // Set author name if not provided
        if (!isset($data['author'])) {
            $data['author'] = $author->name;
        }

        $article = Article::create($data);
        $article->load('authorUser');
        
        // Clear cache setelah create
        $this->clearCache();

        return $article;
    }

    /**
     * Update artikel
     */
    public function updateArticle(Article $article, array $data, $imageFile = null): Article
    {
        $article->update($data);
        $article->load('authorUser');
        
        // Clear cache setelah update
        $this->clearCache();

        return $article;
    }

    /**
     * Hapus artikel
     */
    public function deleteArticle(Article $article): bool
    {
        $result = $article->delete();
        
        // Clear cache setelah delete
        $this->clearCache();

        return $result;
    }

    /**
     * Ambil artikel populer (cached 15 menit)
     */
    public function getPopularArticles(int $limit = 5): Collection
    {
        return Cache::remember("articles:popular:{$limit}", 900, function () use ($limit) {
            return Article::with('authorUser')
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Ambil artikel berdasarkan kategori
     */
    public function getByCategory(string $category, int $perPage = 15): LengthAwarePaginator
    {
        return Article::with('authorUser')
            ->where('category', $category)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Clear semua cache articles
     */
    public function clearCache(): void
    {
        Cache::forget('articles:popular:5');
        Cache::forget('articles:popular:10');
    }
}
