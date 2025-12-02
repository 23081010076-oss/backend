<?php

namespace App\Swagger;

 /**
     * @OA\Get(
     * path="/api/my-courses",
     * summary="Get logged-in user's courses",
     * description="Menampilkan daftar kursus yang sedang diikuti oleh user yang login",
     * operationId="getMyCourses",
     * tags={"Enrollments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="List kursus berhasil diambil",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Kursus Anda berhasil diambil"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="current_page", type="integer"),
     * @OA\Property(property="data", type="array",
     * @OA\Items(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="course_id", type="integer", example=5),
     * @OA\Property(property="progress", type="integer", example=50),
     * @OA\Property(property="completed", type="boolean", example=false),
     * @OA\Property(property="course", type="object",
     * @OA\Property(property="title", type="string", example="Belajar Laravel Dasar"),
     * @OA\Property(property="instructor", type="string", example="John Doe")
     * )
     * )
     * )
     * )
     * )
     * )
     * )
     * * @OA\Get(
     * path="/api/enrollments",
     * summary="Get all enrollments (Admin)",
     * description="Melihat seluruh data enrollment (untuk Admin)",
     * operationId="getIndexEnrollments",
     * tags={"Enrollments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Success"
     * )
     * )
     *
     * @OA\Post(
     * path="/api/courses/{id}/enroll",
     * summary="Enroll to a course",
     * description="Mendaftar ke kursus berdasarkan ID Kursus di URL",
     * operationId="enrollCourse",
     * tags={"Enrollments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID Kursus yang ingin didaftarkan",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=201,
     * description="Berhasil mendaftar",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Berhasil mendaftar ke kursus"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="user_id", type="integer", example=1),
     * @OA\Property(property="course_id", type="integer", example=1),
     * @OA\Property(property="progress", type="integer", example=0)
     * )
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="User sudah terdaftar di kursus ini"
     * ),
     * @OA\Response(
     * response=404,
     * description="Kursus tidak ditemukan"
     * )
     * )
     *
     * @OA\Put(
     * path="/api/enrollments/{id}/progress",
     * summary="Update course progress",
     * description="Update progress belajar user pada enrollment tertentu",
     * operationId="updateProgress",
     * tags={"Enrollments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID Enrollment (Bukan ID Course)",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Data progress baru",
     * @OA\JsonContent(
     * required={"progress"},
     * @OA\Property(property="progress", type="integer", minimum=0, maximum=100, example=75, description="Persentase progress (0-100)")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Progress berhasil diupdate",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Progress berhasil diupdate"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="progress", type="integer", example=75),
     * @OA\Property(property="completed", type="boolean", example=false)
     * )
     * )
     * )
     * )
     */
class EnrollmentSwagger {}
