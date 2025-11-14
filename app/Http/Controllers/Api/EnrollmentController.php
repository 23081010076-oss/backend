<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['user', 'course'])
            ->paginate(15);

        return response()->json($enrollments);
    }

    /**
     * Enroll to a course
     */
    public function enroll($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course'
            ], 422);
        }

        // Check access permission based on subscription
        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
        
        if ($course->access_type === 'premium' && (!$subscription || $subscription->plan !== 'premium')) {
            return response()->json([
                'message' => 'Premium subscription required for this course'
            ], 403);
        }

        if ($course->access_type === 'regular' && (!$subscription || $subscription->plan === 'free')) {
            return response()->json([
                'message' => 'Regular or Premium subscription required for this course'
            ], 403);
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'progress' => 0,
            'completed' => false,
        ]);

        return response()->json([
            'message' => 'Successfully enrolled in course',
            'data' => $enrollment->load('course')
        ], 201);
    }

    /**
     * Get user's enrolled courses
     */
    public function myCourses()
    {
        $user = auth()->user();
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'data' => $enrollments
        ]);
    }

    /**
     * Update enrollment progress
     */
    public function updateProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        // Check ownership
        if ($enrollment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $enrollment->progress = $validated['progress'];
        
        // Auto-complete if progress is 100%
        if ($validated['progress'] >= 100) {
            $enrollment->completed = true;
            // Generate certificate URL
            $enrollment->certificate_url = 'certificates/' . $enrollment->id . '-' . time() . '.pdf';
        }

        $enrollment->save();

        return response()->json([
            'message' => 'Progress updated successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Display the specified enrollment
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($id);

        return response()->json([
            'data' => $enrollment
        ]);
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'progress' => 'sometimes|integer|min:0|max:100',
            'completed' => 'sometimes|boolean',
            'certificate_url' => 'nullable|string',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        
        // Check ownership or admin
        if ($enrollment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully'
        ]);
    }
}
