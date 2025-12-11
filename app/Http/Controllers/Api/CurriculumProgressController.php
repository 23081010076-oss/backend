<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurriculumProgress;
use App\Models\CourseCurriculum;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ✅ NEW: Controller for tracking curriculum progress
 * 
 * Allows users to mark curriculum items as completed
 * and track their overall course progress
 */
class CurriculumProgressController extends Controller
{
    use ApiResponse;

    /**
     * Get user's progress for a specific course
     */
    public function index(Request $request, int $courseId): JsonResponse
    {
        $progress = CurriculumProgress::where('user_id', $request->user()->id)
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
     * Mark curriculum item as completed
     */
    public function markCompleted(Request $request, int $curriculumId): JsonResponse
    {
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $curriculum = CourseCurriculum::findOrFail($curriculumId);

        // Check if user has access to this course
        // You can add enrollment/subscription check here

        $progress = CurriculumProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'curriculum_id' => $curriculumId,
            ],
            [
                'completed' => $validated['completed'],
                'completed_at' => $validated['completed'] ? now() : null,
            ]
        );

        // Update overall enrollment progress
        $this->updateEnrollmentProgress($request->user()->id, $curriculum->course_id);

        return $this->successResponse($progress, 'Progress updated successfully');
    }

    /**
     * Update overall enrollment progress percentage
     * ✅ FIX: Auto-generate certificate when course 100% completed
     */
    private function updateEnrollmentProgress(int $userId, int $courseId): void
    {
        $enrollment = \App\Models\Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return;
        }

        $totalItems = CourseCurriculum::where('course_id', $courseId)->count();
        $completedItems = CurriculumProgress::where('user_id', $userId)
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
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'certificate_url' => $certificateUrl,
                    ]);
                }
            }
        }
        
        $enrollment->save();
    }
}
