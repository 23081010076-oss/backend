<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Request Classes
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;

/**
 * ==========================================================================
 * ARTICLE CONTROLLER (Controller untuk Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola artikel/blog.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/ArticleService.php
 * - Policy      : Authorization  → app/Policies/ArticlePolicy.php
 * - Request     : Validation     → app/Http/Requests/Article/
 */
class ArticleController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected ArticleService $articleService;

    /**
     * Constructor - Inject service
     */
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar artikel dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        $articles = $this->articleService->getArticles($request->all());

        return $this->paginatedResponse($articles, 'Daftar artikel berhasil diambil');
    }

    /**
     * Tampilkan detail artikel
     */
    public function show(int $id): JsonResponse
    {
        $article = Article::with('author')->findOrFail($id);

        // Cek akses dengan Policy (bisa dilihat guest jika published)
        $this->authorize('view', $article);

        // Increment views via service
        $this->articleService->incrementViews($article);

        return $this->successResponse($article, 'Detail artikel berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah artikel baru
     * 
     * Validasi di: app/Http/Requests/Article/StoreArticleRequest.php
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Article::class);

        $article = $this->articleService->createArticle(
            Auth::user(),
            $request->validated(),
            $request->file('image')
        );

        return $this->createdResponse($article, 'Artikel berhasil ditambahkan');
    }

    /**
     * Update artikel
     * 
     * Validasi di: app/Http/Requests/Article/UpdateArticleRequest.php
     */
    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $article);

        $article = $this->articleService->updateArticle(
            $article,
            $request->validated(),
            $request->file('image')
        );

        return $this->successResponse($article, 'Artikel berhasil diupdate');
    }

    /**
     * Hapus artikel
     */
    public function destroy(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $article);

        $this->articleService->deleteArticle($article);

        return $this->successResponse(null, 'Artikel berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Lihat artikel berdasarkan slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $article = Article::with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        // Cek akses dengan Policy
        $this->authorize('view', $article);

        $this->articleService->incrementViews($article);

        return $this->successResponse($article, 'Detail artikel berhasil diambil');
    }

    /**
     * Artikel populer (paling banyak dilihat)
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $articles = $this->articleService->getPopularArticles($limit);

        return $this->successResponse($articles, 'Artikel populer berhasil diambil');
    }

    /**
     * Artikel berdasarkan kategori
     */
    public function byCategory(string $category, Request $request): JsonResponse
    {
        $articles = $this->articleService->getByCategory($category, $request->all());

        return $this->paginatedResponse($articles, "Artikel kategori '{$category}' berhasil diambil");
    }
}
