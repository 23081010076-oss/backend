<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/courses",
 *     summary="Get all courses",
 *     description="Get list of all available courses with pagination",
 *     operationId="getCourses",
 *     tags={"Courses"},
 *     @OA\Parameter(
 *         name="level",
 *         in="query",
 *         description="Filter by course level",
 *         required=false,
 *         @OA\Schema(type="string", enum={"beginner", "intermediate", "advanced"})
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Filter by course type",
 *         required=false,
 *         @OA\Schema(type="string", enum={"course", "bootcamp"})
 *     ),
 *     @OA\Parameter(
 *         name="access_type",
 *         in="query",
 *         description="Filter by access type",
 *         required=false,
 *         @OA\Schema(type="string", enum={"free", "regular", "premium"})
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search courses by title or description",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Courses retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Full Stack Web Development Bootcamp"),
 *                     @OA\Property(property="description", type="string", example="Comprehensive bootcamp covering..."),
 *                     @OA\Property(property="type", type="string", example="bootcamp"),
 *                     @OA\Property(property="level", type="string", example="beginner"),
 *                     @OA\Property(property="instructor", type="string", example="Dr. Ahmad Syafiq"),
 *                     @OA\Property(property="duration", type="string", example="12 weeks"),
 *                     @OA\Property(property="price", type="number", format="float", example=2500000),
 *                     @OA\Property(property="access_type", type="string", example="premium"),
 *                     @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/..."),
 *                     @OA\Property(property="total_videos", type="integer", example=45)
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/courses/{id}",
 *     summary="Get course detail",
 *     description="Get detailed information about a specific course including curriculums",
 *     operationId="getCourseById",
 *     tags={"Courses"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Course ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Course detail retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Detail kursus berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Full Stack Web Development Bootcamp"),
 *                 @OA\Property(property="description", type="string", example="Comprehensive bootcamp covering HTML, CSS, JavaScript, React, Node.js, and database management."),
 *                 @OA\Property(property="category", type="string", example="Web Development"),
 *                 @OA\Property(property="type", type="string", example="bootcamp"),
 *                 @OA\Property(property="level", type="string", example="beginner"),
 *                 @OA\Property(property="instructor", type="string", example="Dr. Ahmad Syafiq"),
 *                 @OA\Property(property="duration", type="string", example="12 weeks"),
 *                 @OA\Property(property="price", type="number", example=2500000),
 *                 @OA\Property(property="access_type", type="string", example="premium"),
 *                 @OA\Property(property="total_videos", type="integer", example=45),
 *                 @OA\Property(property="total_materials", type="integer", example=7, description="Auto-calculated from curriculums"),
 *                 @OA\Property(property="total_curriculum_duration", type="string", example="60 jam", description="Auto-calculated total duration"),
 *                 @OA\Property(property="curriculums", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="section", type="string", example="Bab 1: Dasar-dasar Web"),
 *                         @OA\Property(property="section_order", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="Pengenalan Web Development"),
 *                         @OA\Property(property="description", type="string", example="Memahami dasar-dasar web dan cara kerja internet"),
 *                         @OA\Property(property="order", type="integer", example=1),
 *                         @OA\Property(property="duration", type="string", example="2 jam"),
 *                         @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/pengenalan-web")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Course not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/courses",
 *     summary="Create new course",
 *     description="Create a new course (Admin only)",
 *     operationId="createCourse",
 *     tags={"Courses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","description","type","level"},
 *             @OA\Property(property="title", type="string", example="React.js Advanced"),
 *             @OA\Property(property="description", type="string", example="Learn advanced React concepts"),
 *             @OA\Property(property="type", type="string", enum={"course", "bootcamp"}, example="course"),
 *             @OA\Property(property="level", type="string", enum={"beginner", "intermediate", "advanced"}, example="advanced"),
 *             @OA\Property(property="instructor", type="string", example="John Smith"),
 *             @OA\Property(property="duration", type="string", example="8 weeks"),
 *             @OA\Property(property="price", type="number", example=1500000),
 *             @OA\Property(property="access_type", type="string", enum={"free", "regular", "premium"}, example="premium"),
 *             @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/xyz"),
 *             @OA\Property(property="total_videos", type="integer", example=35)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Course created successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin only"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/courses/{id}",
 *     summary="Update course",
 *     description="Update an existing course (Admin only)",
 *     operationId="updateCourse",
 *     tags={"Courses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Course ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="price", type="number")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Course updated successfully"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/courses/{id}",
 *     summary="Delete course",
 *     description="Delete a course (Admin only)",
 *     operationId="deleteCourse",
 *     tags={"Courses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Course ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Course deleted successfully"
 *     )
 * )
 */
class CourseSwagger {}
