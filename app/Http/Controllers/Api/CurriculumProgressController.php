<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurriculumProgress;
use App\Models\CourseCurriculum;
use App\Models\Enrollment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ✅ Controller for tracking curriculum progress
 * 
 * Allows users to mark curriculum items as completed
 * and track their overall course progress
 */
class CurriculumProgressController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/courses/{courseId}/progress",
     *     tags={"Progress"},
     *     summary="Mendapatkan progress kursus",
     *     description="Menampilkan progress penyelesaian kursus oleh user yang sedang login",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID kursus",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progress berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Progress retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="progress",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="enrollment_id", type="integer", example=5),
     *                         @OA\Property(property="curriculum_id", type="integer", example=2),
     *                         @OA\Property(property="completed", type="boolean", example=true),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                         @OA\Property(
     *                             property="curriculum",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="course_id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="Pengenalan Laravel"),
     *                             @OA\Property(property="section", type="string", example="Bab 1: Dasar-Dasar")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="statistics",
     *                     type="object",
     *                     @OA\Property(property="total_items", type="integer", example=20),
     *                     @OA\Property(property="completed_items", type="integer", example=8),
     *                     @OA\Property(property="percentage", type="number", format="float", example=40.0)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User belum terdaftar di kursus ini",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda belum terdaftar di kursus ini")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * Get user's progress for a specific course
     */
    public function index(Request $request, int $courseId): JsonResponse
    {
        // Get user's enrollment for this course
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return $this->errorResponse('Anda belum terdaftar di kursus ini', 403);
        }

        $progress = CurriculumProgress::where('enrollment_id', $enrollment->id)
            ->whereHas('curriculum', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with('curriculum:id,course_id,title,section')
            ->get();

        $totalItems = CourseCurriculum::where('course_id', $courseId)->count();
        $completedItems = $progress->where('completed', true)->count();
        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0;

        return $this->successResponse([
            'progress' => $progress,
            'statistics' => [
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
                'percentage' => $percentage,
            ],
        ], 'Progress retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/curriculums/{curriculumId}/complete",
     *     tags={"Progress"},
     *     summary="Tandai materi selesai",
     *     description="Menandai item curriculum sebagai selesai. Progress enrollment akan otomatis terupdate dan sertifikat akan otomatis dibuat jika progress mencapai 100%",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="curriculumId",
     *         in="path",
     *         required=true,
     *         description="ID curriculum",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"completed"},
     *             @OA\Property(
     *                 property="completed",
     *                 type="boolean",
     *                 description="Status penyelesaian (true = selesai, false = belum selesai)",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progress berhasil diupdate",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Progress updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="sukses", type="boolean", example=true),
     *                 @OA\Property(property="pesan", type="string", example="Materi berhasil ditandai selesai"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="curriculum_progress",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="enrollment_id", type="integer", example=5),
     *                         @OA\Property(property="curriculum_id", type="integer", example=2),
     *                         @OA\Property(property="completed", type="boolean", example=true),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     ),
     *                     @OA\Property(
     *                         property="enrollment",
     *                         type="object",
     *                         @OA\Property(property="progress", type="integer", description="Persentase progress (0-100)", example=45),
     *                         @OA\Property(property="completed", type="boolean", description="Apakah kursus sudah selesai 100%", example=false)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User belum terdaftar di kursus ini",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda belum terdaftar di kursus ini")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curriculum tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\CourseCurriculum]")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The completed field is required."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="completed",
     *                     type="array",
     *                     @OA\Items(type="string", example="The completed field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * Mark curriculum item as completed
     */
    public function markCompleted(Request $request, int $curriculumId): JsonResponse
    {
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $curriculum = CourseCurriculum::findOrFail($curriculumId);

        // Get user's enrollment for this course
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $curriculum->course_id)
            ->first();

        if (!$enrollment) {
            return $this->errorResponse('Anda belum terdaftar di kursus ini', 403);
        }

        $progress = CurriculumProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'curriculum_id' => $curriculumId,
            ],
            [
                'completed' => $validated['completed'],
                'completed_at' => $validated['completed'] ? now() : null,
            ]
        );

        // Update overall enrollment progress
        $this->updateEnrollmentProgress($enrollment);

        return $this->successResponse([
            'sukses' => true,
            'pesan' => 'Materi berhasil ditandai selesai',
            'data' => [
                'curriculum_progress' => $progress,
                'enrollment' => [
                    'progress' => $enrollment->progress,
                    'completed' => $enrollment->completed,
                ]
            ]
        ], 'Progress updated successfully');
    }

    /**
     * Update overall enrollment progress percentage
     * ✅ FIX: Auto-generate certificate when course 100% completed
     */
    private function updateEnrollmentProgress(Enrollment $enrollment): void
    {
        $courseId = $enrollment->course_id;

        $totalItems = CourseCurriculum::where('course_id', $courseId)->count();
        $completedItems = CurriculumProgress::where('enrollment_id', $enrollment->id)
            ->whereHas('curriculum', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where('completed', true)
            ->count();

        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        // Update progress
        $enrollment->progress = $percentage;
        
        // ✅ FIX: Auto-generate certificate when reaching 100%
        if ($percentage >= 100 && !$enrollment->completed) {
            $enrollment->completed = true;
            
            // Generate certificate (only if not exists)
            if (!$enrollment->certificate_url) {
                $enrollmentService = app(\App\Services\EnrollmentService::class);
                $certificateUrl = $enrollmentService->generateCertificate($enrollment);
                
                if ($certificateUrl) {
                    $enrollment->certificate_url = $certificateUrl;
                    
                    \Illuminate\Support\Facades\Log::info('Certificate auto-generated on course completion', [
                        'enrollment_id' => $enrollment->id,
                        'user_id' => $enrollment->user_id,
                        'course_id' => $courseId,
                        'certificate_url' => $certificateUrl,
                    ]);
                }
            }
        }
        
        $enrollment->save();
    }
}
