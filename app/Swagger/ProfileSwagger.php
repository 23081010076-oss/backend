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
 *     summary="Get personalized course recommendations",
 *     description="Get AI-powered course recommendations based on user's specialization (interests), major, subscription plan, and engagement metrics. **Algorithm Priority:** 1) **Specialization/Interests** (150-80 points) - highest priority matching with user's declared interests, 2) **Major/Field of Study** (60-30 points) - matches with user's educational background, 3) **Rating & Popularity** - considers course quality and enrollment count, 4) **Subscription Access** - filters based on user's plan (free/regular/premium), 5) **Enrollment Status** - excludes already enrolled courses",
 *     operationId="getRecommendations",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of course recommendations to return (default: 5, max recommended: 20)",
 *         required=false,
 *         @OA\Schema(type="integer", example=5, default=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Personalized course recommendations retrieved successfully with relevance scoring",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Rekomendasi kursus berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="recommendations", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="React Advanced Development"),
 *                         @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/courses/react-course.jpg"),
 *                         @OA\Property(property="description", type="string", example="Master React with hooks, context, and advanced patterns"),
 *                         @OA\Property(property="type", type="string", example="bootcamp", description="online, offline, bootcamp"),
 *                         @OA\Property(property="category", type="string", example="Web Development"),
 *                         @OA\Property(property="level", type="string", example="intermediate", description="beginner, intermediate, advanced"),
 *                         @OA\Property(property="duration", type="string", example="8 minggu"),
 *                         @OA\Property(property="price", type="number", format="decimal", example=1500000.00),
 *                         @OA\Property(property="access_type", type="string", example="premium", description="free, regular, premium"),
 *                         @OA\Property(property="instructor", type="string", example="Sarah Johnson"),
 *                         @OA\Property(property="enrollments_count", type="integer", example=245, description="Total number of enrolled students"),
 *                         @OA\Property(property="reviews_avg_rating", type="number", format="float", example=4.8, description="Average rating from reviews"),
 *                         @OA\Property(property="average_rating", type="number", format="float", example=4.8, description="Average rating (appended attribute)"),
 *                         @OA\Property(property="total_reviews", type="integer", example=89, description="Total number of reviews"),
 *                         @OA\Property(property="total_materials", type="integer", example=42, description="Total curriculum items"),
 *                         @OA\Property(property="total_curriculum_duration", type="string", example="12 jam 30 menit"),
 *                         @OA\Property(property="relevance_score", type="integer", example=150, description="Scoring based on specialization & major matching (0-150). Higher = more relevant. Only present if user has specialization or major.")
 *                     )
 *                 ),
 *                 @OA\Property(property="criteria", type="object", description="Recommendation algorithm criteria used",
 *                     @OA\Property(property="subscription_plan", type="string", example="premium", description="User's current subscription plan"),
 *                     @OA\Property(property="specializations", type="array", 
 *                         @OA\Items(type="string", example="Web Development"),
 *                         example={"Web Development", "React", "UI/UX Design"},
 *                         description="User's interests/specializations used for matching"
 *                     ),
 *                     @OA\Property(property="major", type="string", example="Teknik Informatika", description="User's field of study"),
 *                     @OA\Property(property="excluded_enrolled", type="integer", example=3, description="Number of already enrolled courses excluded"),
 *                     @OA\Property(property="algorithm", type="string", example="specialization_score + major_score + rating + popularity", description="Algorithm formula used")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Invalid or missing JWT token",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
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
