<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'error' => 'The provided credentials are incorrect.'
                ], 401);
            }

            $token = JWTAuth::fromUser($user);
            $ttl = config('jwt.ttl', 60); // Default 60 minutes

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
                    'expires_in' => $ttl * 60,
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Get current authenticated user (me)
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'message' => 'User retrieved successfully',
                'data' => [
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
                    'gender' => $user->gender,
                    'birth_date' => $user->birth_date,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh JWT token
     */
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            $ttl = config('jwt.ttl', 60);
            
            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => $ttl * 60,
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not refresh token',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'error' => 'The provided current password does not match our records.'
                ], 401);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load(['achievements', 'experiences', 'subscriptions']);
            
            return response()->json([
                'message' => 'Profile retrieved successfully',
                'data' => [
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update authenticated user profile
     */
    public function updateProfile(Request $request)
    {
        try {
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
                'data' => [
                    'user' => $user->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = $request->user();

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo = $path;
                $user->save();
            }

            return response()->json([
                'message' => 'Profile photo uploaded successfully',
                'data' => [
                    'profile_photo' => $user->profile_photo,
                    'profile_photo_url' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload profile photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload CV
     */
    public function uploadCv(Request $request)
    {
        try {
            $request->validate([
                'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);

            $user = $request->user();

            if ($request->hasFile('cv')) {
                // Delete old CV if exists
                if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
                    Storage::disk('public')->delete($user->cv_path);
                }

                $path = $request->file('cv')->store('cvs', 'public');
                $user->cv_path = $path;
                $user->save();
            }

            return response()->json([
                'message' => 'CV uploaded successfully',
                'data' => [
                    'cv_path' => $user->cv_path,
                    'cv_url' => $user->cv_path ? asset('storage/' . $user->cv_path) : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload CV',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommendations based on user profile
     */
    public function recommendations(Request $request)
    {
        try {
            $user = $request->user();
            $major = $user->major;

            // Basic recommendation logic
            $recommendedCourses = \App\Models\Course::query();

            if ($major) {
                $recommendedCourses->where(function($query) use ($major) {
                    $query->where('title', 'like', '%' . $major . '%')
                          ->orWhere('description', 'like', '%' . $major . '%');
                });
                $recommendations = $recommendedCourses->limit(5)->get();
            } else {
                $recommendations = collect();
            }

            // If no specific matches, return popular/random courses
            if ($recommendations->isEmpty()) {
                $recommendations = \App\Models\Course::inRandomOrder()->limit(5)->get();
            }

            return response()->json([
                'message' => 'Recommendations retrieved successfully',
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's complete portfolio
     */
    public function portfolio(Request $request)
    {
        try {
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
                'message' => 'Portfolio retrieved successfully',
                'data' => [
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
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve portfolio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's activity history
     */
    public function activityHistory(Request $request)
    {
        try {
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
                'message' => 'Activity history retrieved successfully',
                'data' => [
                    'summary' => $activities,
                    'recent' => $recentActivities,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve activity history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
