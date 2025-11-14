<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by access_type
        if ($request->has('access_type')) {
            $query->where('access_type', $request->access_type);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->paginate(15);

        return response()->json($courses);
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:bootcamp,course',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'access_type' => 'required|in:free,regular,premium',
            'certificate_url' => 'nullable|string',
        ]);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course
        ], 201);
    }

    /**
     * Display the specified course
     */
    public function show($id)
    {
        $course = Course::with('enrollments')->findOrFail($id);
        
        return response()->json([
            'data' => $course
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:bootcamp,course',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'duration' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'access_type' => 'sometimes|in:free,regular,premium',
            'certificate_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,flv|max:524288', // 500MB max
            'video_url' => 'nullable|string|url', // For embed URLs like YouTube
            'video_duration' => 'nullable|string', // HH:MM:SS format
        ]);

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $path = $file->store('course-videos', 'public');
            $validated['video_url'] = $path;
        }

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    }

    /**
     * Remove the specified course
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Delete video file if exists
        if ($course->video_url && Storage::disk('public')->exists($course->video_url)) {
            Storage::disk('public')->delete($course->video_url);
        }
        
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }
}
