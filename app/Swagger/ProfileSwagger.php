<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/auth/profile/photo",
 *     summary="Upload profile photo",
 *     description="Upload user profile photo",
 *     operationId="uploadProfilePhoto",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"profile_photo"},
 *                 @OA\Property(property="profile_photo", type="string", format="binary", description="Profile photo (JPEG, PNG, JPG, GIF, max 2MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Photo uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Profile photo uploaded successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="profile_photo_url", type="string", example="http://127.0.0.1:8000/storage/profile-photos/abc123.jpg")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/auth/profile/cv",
 *     summary="Upload CV",
 *     description="Upload user CV document",
 *     operationId="uploadCv",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"cv"},
 *                 @OA\Property(property="cv", type="string", format="binary", description="CV document (PDF, DOC, DOCX, max 2MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="CV uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="CV berhasil diupload"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="cv_path", type="string", example="http://127.0.0.1:8000/storage/cvs/cv_123.pdf")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/recommendations",
 *     summary="Get recommendations",
 *     description="Get personalized recommendations for courses, scholarships, etc. (based on user's major)",
 *     operationId="getRecommendations",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Personalized recommendations retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="courses", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="Full Stack Web Development"),
 *                         @OA\Property(property="type", type="string", example="bootcamp"),
 *                         @OA\Property(property="level", type="string", example="intermediate")
 *                     )
 *                 ),
 *                 @OA\Property(property="scholarships", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="LPDP Scholarship"),
 *                         @OA\Property(property="status", type="string", example="open")
 *                     )
 *                 ),
 *                 @OA\Property(property="mentors", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=7),
 *                         @OA\Property(property="name", type="string", example="Dr. Tech Expert"),
 *                         @OA\Property(property="major", type="string", example="Computer Science")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/portfolio",
 *     summary="Get user portfolio",
 *     description="Get user portfolio including achievements, experiences, organizations, courses, scholarship applications, mentoring sessions, and subscriptions",
 *     operationId="getPortfolioAuth",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User portfolio retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="profile", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Ahmad Rizki"),
 *                     @OA\Property(property="email", type="string", example="ahmad@example.com"),
 *                     @OA\Property(property="major", type="string", example="Computer Science")
 *                 ),
 *                 @OA\Property(property="prestasi", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="title", type="string", example="Best Graduate Award"),
 *                         @OA\Property(property="issuer", type="string", example="Universitas Indonesia")
 *                     )
 *                 ),
 *                 @OA\Property(property="pengalaman", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="title", type="string", example="Software Engineer Intern"),
 *                         @OA\Property(property="company", type="string", example="Google Indonesia")
 *                     )
 *                 ),
 *                 @OA\Property(property="organisasi", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="name", type="string", example="Tech Student Association")
 *                     )
 *                 ),
 *                 @OA\Property(property="kursus", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="course_title", type="string", example="React Development"),
 *                         @OA\Property(property="progress", type="integer", example=75)
 *                     )
 *                 ),
 *                 @OA\Property(property="lamaran_beasiswa", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="scholarship_name", type="string", example="LPDP"),
 *                         @OA\Property(property="status", type="string", example="pending")
 *                     )
 *                 ),
 *                 @OA\Property(property="sesi_mentoring", type="object",
 *                     @OA\Property(property="sebagai_murid", type="array", @OA\Items(type="object")),
 *                     @OA\Property(property="sebagai_mentor", type="array", @OA\Items(type="object"))
 *                 ),
 *                 @OA\Property(property="langganan", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="plan", type="string", example="premium"),
 *                         @OA\Property(property="status", type="string", example="active")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/activity-history",
 *     summary="Get activity history",
 *     description="Get user activity history with summary and latest activities",
 *     operationId="getActivityHistory",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User activity history retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="ringkasan", type="object",
 *                     @OA\Property(property="kursus_selesai", type="integer", example=5),
 *                     @OA\Property(property="kursus_sedang_diambil", type="integer", example=2),
 *                     @OA\Property(property="mentoring_selesai", type="integer", example=3),
 *                     @OA\Property(property="lamaran_beasiswa", type="integer", example=2),
 *                     @OA\Property(property="jumlah_prestasi", type="integer", example=4),
 *                     @OA\Property(property="jumlah_pengalaman", type="integer", example=3),
 *                     @OA\Property(property="jumlah_organisasi", type="integer", example=2)
 *                 ),
 *                 @OA\Property(property="terbaru", type="object",
 *                     @OA\Property(property="kursus_terbaru", type="array",
 *                         @OA\Items(type="object",
 *                             @OA\Property(property="course_title", type="string", example="React Development"),
 *                             @OA\Property(property="enrolled_at", type="string", format="datetime")
 *                         )
 *                     ),
 *                     @OA\Property(property="lamaran_terbaru", type="array",
 *                         @OA\Items(type="object",
 *                             @OA\Property(property="scholarship_name", type="string", example="LPDP"),
 *                             @OA\Property(property="applied_at", type="string", format="datetime")
 *                         )
 *                     ),
 *                     @OA\Property(property="mentoring_terbaru", type="array",
 *                         @OA\Items(type="object",
 *                             @OA\Property(property="mentor_name", type="string", example="Dr. Tech Expert"),
 *                             @OA\Property(property="schedule", type="string", format="datetime")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
class ProfileSwagger {}
