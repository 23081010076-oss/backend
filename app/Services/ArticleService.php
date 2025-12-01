<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ==========================================================================
 * ARTICLE SERVICE (Service untuk Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen artikel.
 * 
 * KENAPA PAKAI SERVICE?
 * - Controller jadi bersih (hanya terima request & return response)
 * - Logika upload gambar, generate slug ada di sini
 * - Mudah dipakai ulang di tempat lain
 */
class ArticleService
{
    /**
     * Ambil daftar artikel dengan filter
     */
    public function getArticles(array $filters = [], bool $isAdmin = false, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::with('author');

        // Filter berdasarkan kategori
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Filter berdasarkan tag
        if (!empty($filters['tag'])) {
            $query->where('tags', 'like', '%' . $filters['tag'] . '%');
        }

        // Pencarian berdasarkan judul atau konten
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Hanya artikel published untuk non-admin
        if (!$isAdmin) {
            $query->where('status', 'published');
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Buat artikel baru
     */
    public function createArticle(array $data, User $author, $imageFile = null): Article
    {
        // Handle upload gambar
        if ($imageFile) {
            $path = $imageFile->store('articles', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Generate slug unik
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['author_id'] = $author->id;

        // Set default status jika tidak ada
        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        $article = Article::create($data);
        $article->load('author');

        return $article;
    }

    /**
     * Update artikel
     */
    public function updateArticle(Article $article, array $data, $imageFile = null): Article
    {
        // Handle upload gambar baru
        if ($imageFile) {
            // Hapus gambar lama
            $this->deleteImage($article->image_url);
            
            // Upload gambar baru
            $path = $imageFile->store('articles', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Update slug jika judul berubah
        if (isset($data['title']) && $data['title'] !== $article->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        $article->update($data);
        $article->load('author');

        return $article;
    }

    /**
     * Hapus artikel
     */
    public function deleteArticle(Article $article): bool
    {
        // Hapus gambar jika ada
        $this->deleteImage($article->image_url);

        return $article->delete();
    }

    /**
     * Tambah view count
     */
    public function incrementViews(Article $article): void
    {
        $article->increment('views');
    }

    /**
     * Ambil artikel populer
     */
    public function getPopularArticles(int $limit = 5): Collection
    {
        return Article::with('author')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil artikel berdasarkan kategori
     */
    public function getByCategory(string $category, int $perPage = 15): LengthAwarePaginator
    {
        return Article::with('author')
            ->where('category', $category)
            ->where('status', 'published')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Generate slug unik
     */
    private function generateUniqueSlug(string $title): string
    {
        return Str::slug($title) . '-' . Str::random(5);
    }

    /**
     * Hapus file gambar
     */
    private function deleteImage(?string $imageUrl): void
    {
        if ($imageUrl) {
            $path = str_replace('/storage/', '', $imageUrl);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
