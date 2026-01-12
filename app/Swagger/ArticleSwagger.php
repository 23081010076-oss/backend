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
 *                     @OA\Property(property="image", type="string", nullable=true, example="articles/article_1735123456_abc123def.jpg"),
 *                     @OA\Property(property="image_url", type="string", nullable=true, example="http://localhost:8000/storage/articles/article_1735123456_abc123def.jpg"),
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
 *     description="Create a new article with optional image upload (Admin/Corporate only). Use multipart/form-data for image upload.",
 *     operationId="createArticle",
 *     tags={"Articles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"title","content"},
 *                 @OA\Property(property="title", type="string", example="Panduan Karir di Tech Industry"),
 *                 @OA\Property(property="content", type="string", example="Artikel lengkap tentang karir di industri teknologi..."),
 *                 @OA\Property(property="category", type="string", enum={"education", "career", "scholarship", "testimonial"}, example="career"),
 *                 @OA\Property(property="author", type="string", example="John Doe"),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Article image (jpeg, jpg, png, gif, webp, max 5MB)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Artikel berhasil ditambahkan"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Panduan Karir di Tech Industry"),
 *                 @OA\Property(property="content", type="string"),
 *                 @OA\Property(property="category", type="string", example="career"),
 *                 @OA\Property(property="author", type="string", example="John Doe"),
 *                 @OA\Property(property="image", type="string", example="articles/article_1735123456_abc123def.jpg"),
 *                 @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/articles/article_1735123456_abc123def.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="datetime")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=false),
 *             @OA\Property(property="pesan", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="Format gambar harus jpeg, jpg, png, gif, atau webp"))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/articles/{id}",
 *     summary="Update article",
 *     description="Update an existing article with optional image upload. Use multipart/form-data for image upload. Note: Use POST method with _method=PUT for multipart requests.",
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
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="title", type="string", example="Panduan Karir di Tech Industry (Updated)"),
 *                 @OA\Property(property="content", type="string", example="Konten artikel yang diupdate..."),
 *                 @OA\Property(property="category", type="string", enum={"education", "career", "scholarship", "testimonial"}),
 *                 @OA\Property(property="author", type="string", example="John Doe"),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="New article image (jpeg, jpg, png, gif, webp, max 5MB). Old image will be deleted if new one is uploaded."
 *                 ),
 *                 @OA\Property(
 *                     property="_method",
 *                     type="string",
 *                     example="PUT",
 *                     description="Required for multipart PUT request"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Artikel berhasil diupdate"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="image", type="string", nullable=true),
 *                 @OA\Property(property="image_url", type="string", nullable=true),
 *                 @OA\Property(property="updated_at", type="string", format="datetime")
 *             )
 *         )
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
 *
 * @OA\Get(
 *     path="/api/articles/popular",
 *     summary="Get popular articles",
 *     description="Get list of most popular articles (Public)",
 *     operationId="getPopularArticles",
 *     tags={"Articles"},
 *     @OA\Parameter(name="limit", in="query", description="Jumlah artikel", @OA\Schema(type="integer", default=5)),
 *     @OA\Response(
 *         response=200,
 *         description="Popular articles retrieved successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/articles/category/{category}",
 *     summary="Get articles by category",
 *     description="Get list of articles filtered by category (Public)",
 *     operationId="getArticlesByCategory",
 *     tags={"Articles"},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Article category",
 *         required=true,
 *         @OA\Schema(type="string", enum={"education", "career", "scholarship", "testimonial"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Articles retrieved successfully"
 *     )
 * )
 */
class ArticleSwagger {}
