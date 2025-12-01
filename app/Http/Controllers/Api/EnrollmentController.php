<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class EnrollmentController
 * 
 * Handles HTTP requests related to course enrollments.
 * Uses EnrollmentService for business logic and EnrollmentPolicy for authorization.
 * 
 * @package App\Http\Controllers\Api
 */
class EnrollmentController extends Controller
{
    use ApiResponse;

    /**
     * @var EnrollmentService
     */
    protected EnrollmentService $enrollmentService;

    /**
     * Create a new controller instance
     *
     * @param EnrollmentService $enrollmentService
     */
    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display a listing of enrollments
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Enrollment::class);

        $enrollments = Enrollment::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($enrollments, 'Enrollments retrieved successfully');
    }

    /**
     * Enroll to a course
     *
     * @param int $courseId
     * @return JsonResponse
     */
    public function enroll(int $courseId): JsonResponse
    {
        $this->authorize('create', Enrollment::class);

        $course = Course::findOrFail($courseId);
        $user = auth()->user();

        try {
            $enrollment = $this->enrollmentService->enrollUserToCourse($user, $course);

            return $this->createdResponse(
                $enrollment->load('course'),
                'Successfully enrolled in course'
            );
        } catch (\InvalidArgumentException $e) {
            $statusCode = str_contains($e->getMessage(), 'already enrolled') ? 422 : 403;
            return $this->errorResponse($e->getMessage(), $statusCode);
        } catch (\Exception $e) {
            Log::error('Enrollment failed in controller', [
                'user_id' => $user->id,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to enroll in course');
        }
    }

    /**
     * Get user's enrolled courses
     *
     * @return JsonResponse
     */
    public function myCourses(): JsonResponse
    {
        $user = auth()->user();
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($enrollments, 'Your courses retrieved successfully');
    }

    /**
     * Update enrollment progress
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('updateProgress', $enrollment);

        try {
            $enrollment = $this->enrollmentService->updateProgress(
                $enrollment,
                $validated['progress']
            );

            return $this->successResponse($enrollment, 'Progress updated successfully');
        } catch (\Exception $e) {
            Log::error('Progress update failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to update progress');
        }
    }

    /**
     * Display the specified enrollment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($id);
        $this->authorize('view', $enrollment);

        return $this->successResponse($enrollment, 'Enrollment retrieved successfully');
    }

    /**
     * Update the specified enrollment
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('update', $enrollment);

        $validated = $request->validate([
            'progress' => 'sometimes|integer|min:0|max:100',
            'completed' => 'sometimes|boolean',
            'certificate_url' => 'nullable|url',
        ]);

        try {
            $enrollment->update($validated);

            return $this->successResponse($enrollment->fresh(), 'Enrollment updated successfully');
        } catch (\Exception $e) {
            Log::error('Enrollment update failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to update enrollment');
        }
    }

    /**
     * Remove the specified enrollment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('delete', $enrollment);

        try {
            $enrollment->delete();
            return $this->successResponse(null, 'Enrollment deleted successfully');
        } catch (\Exception $e) {
            Log::error('Enrollment deletion failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to delete enrollment');
        }
    }
}
