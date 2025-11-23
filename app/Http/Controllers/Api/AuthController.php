<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,mentor,admin,corporate',
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Registration successful. Please login to continue.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token generation failed'], 500);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ]
        ]);

    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalidation failed'], 500);
        }

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load(['achievements', 'experiences', 'subscriptions']),
        ]);
    }

    /**
     * Update authenticated user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'institution' => 'nullable|string',
            'major' => 'nullable|string',
            'education_level' => 'nullable|string',
            'bio' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Upload profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile photo uploaded successfully',
            'profile_photo' => $user->profile_photo,
        ]);
    }

    /**
     * Upload CV
     */
    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('cvs', 'public');
            $user->cv_path = $path;
            $user->save();
        }

        return response()->json([
            'message' => 'CV uploaded successfully',
            'cv_path' => $user->cv_path,
        ]);
    }

    /**
     * Get recommendations based on user profile
     */
    public function recommendations(Request $request)
    {
        $user = $request->user();
        $major = $user->major;
        $interests = $user->bio; // Assuming bio might contain keywords

        // Basic recommendation logic
        $recommendedCourses = \App\Models\Course::query();

        if ($major) {
            $recommendedCourses->where('title', 'like', '%' . $major . '%')
                               ->orWhere('description', 'like', '%' . $major . '%');
        }

        // If no specific matches, return popular/random courses
        $recommendations = $recommendedCourses->limit(5)->get();

        if ($recommendations->isEmpty()) {
            $recommendations = \App\Models\Course::inRandomOrder()->limit(5)->get();
        }

        return response()->json([
            'message' => 'Recommendations retrieved successfully',
            'data' => $recommendations
        ]);
    }

    /**
     * Get user's complete portfolio
     */
    public function portfolio(Request $request)
    {
        $user = $request->user()->load([
            'achievements',
            'experiences',
            'organizations',
            'enrollments.course',
            'scholarshipApplications.scholarship',
            'mentoringSessionsAsStudent',
            'mentoringSessionsAsMentor',
            'subscriptions'
        ]);

        return response()->json([
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'institution' => $user->institution,
                'major' => $user->major,
                'education_level' => $user->education_level,
                'bio' => $user->bio,
                'profile_photo' => $user->profile_photo,
                'cv_path' => $user->cv_path,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
            ],
            'achievements' => $user->achievements,
            'experiences' => $user->experiences,
            'organizations' => $user->organizations,
            'courses' => $user->enrollments,
            'scholarship_applications' => $user->scholarshipApplications,
            'mentoring_sessions' => [
                'as_student' => $user->mentoringSessionsAsStudent,
                'as_mentor' => $user->mentoringSessionsAsMentor,
            ],
            'subscriptions' => $user->subscriptions,
        ]);
    }

    /**
     * Get user's activity history
     */
    public function activityHistory(Request $request)
    {
        $user = $request->user();

        $activities = [
            'courses_completed' => $user->enrollments()->where('completed', true)->count(),
            'courses_in_progress' => $user->enrollments()->where('completed', false)->count(),
            'mentoring_sessions_completed' => $user->mentoringSessionsAsStudent()
                ->where('status', 'completed')->count(),
            'scholarship_applications' => $user->scholarshipApplications()->count(),
            'achievements' => $user->achievements()->count(),
            'experiences' => $user->experiences()->count(),
            'organizations' => $user->organizations()->count(),
        ];

        $recentActivities = [
            'recent_enrollments' => $user->enrollments()
                ->with('course')
                ->latest()
                ->limit(5)
                ->get(),
            'recent_applications' => $user->scholarshipApplications()
                ->with('scholarship')
                ->latest()
                ->limit(5)
                ->get(),
            'recent_sessions' => $user->mentoringSessionsAsStudent()
                ->with('mentor')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'summary' => $activities,
            'recent' => $recentActivities,
        ]);
    }
}
