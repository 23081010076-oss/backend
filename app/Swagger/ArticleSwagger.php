<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/articles",
 *     summary="Get all articles",
 *     description="Get list of all published articles",
 *     operationId="getArticles",
 *     tags={"Articles"},
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Filter by category",
 *         required=false,
 *         @OA\Schema(type="string", enum={"education", "career", "scholarship", "testimonial"})
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search articles by title or content",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Articles retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="5 Tips Memilih Jurusan Kuliah"),
 *                     @OA\Property(property="content", type="string", example="Memilih jurusan kuliah adalah..."),
 *                     @OA\Property(property="category", type="string", example="education"),
 *                     @OA\Property(property="author", type="string", example="Dr. Sari Wijayanti"),
 *                     @OA\Property(property="created_at", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/articles/{id}",
 *     summary="Get article detail",
 *     description="Get detailed article content",
 *     operationId="getArticleById",
 *     tags={"Articles"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Article ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article detail retrieved"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Article not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/articles",
 *     summary="Create article",
 *     description="Create a new article (Admin/Corporate only)",
 *     operationId="createArticle",
 *     tags={"Articles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","content","category"},
 *             @OA\Property(property="title", type="string", example="Panduan Karir di Tech Industry"),
 *             @OA\Property(property="content", type="string", example="Artikel lengkap tentang..."),
 *             @OA\Property(property="category", type="string", enum={"education", "career", "scholarship", "testimonial"}),
 *             @OA\Property(property="author", type="string", example="John Doe")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article created successfully"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/articles/{id}",
 *     summary="Update article",
 *     description="Update an existing article",
 *     operationId="updateArticle",
 *     tags={"Articles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="content", type="string"),
 *             @OA\Property(property="category", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article updated successfully"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/articles/{id}",
 *     summary="Delete article",
 *     description="Delete an article",
 *     operationId="deleteArticle",
 *     tags={"Articles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article deleted successfully"
 *     )
 * )
 */
class ArticleSwagger {}
