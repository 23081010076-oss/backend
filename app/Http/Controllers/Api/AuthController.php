<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

// ============================================================================
// IMPORT REQUEST & RESOURCE CLASSES
// ============================================================================
// Request = untuk validasi input (agar controller lebih bersih)
// Resource = untuk format output (agar response konsisten)
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendWelcomeEmail;
use App\Models\Subscription;
use Carbon\Carbon;

/**
 * ==========================================================================
 * AUTH CONTROLLER (Controller untuk Autentikasi)
 * ==========================================================================
 *
 * FUNGSI: Menangani semua hal tentang akun pengguna:
 * - Daftar akun baru (register)
 * - Masuk/Login
 * - Keluar/Logout
 * - Ganti password
 * - Kelola profil pengguna
 * - Upload foto dan CV
 * - Melihat portofolio
 *
 * CATATAN PENTING:
 * - Validasi input sudah dipindahkan ke folder app/Http/Requests
 * - Format output sudah dipindahkan ke folder app/Http/Resources
 * - Ini membuat controller lebih mudah dibaca dan dipahami
 */
class AuthController extends Controller
{
    use ApiResponse;

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: AUTENTIKASI (Login, Register, Logout)
    |--------------------------------------------------------------------------
    */

    /**
     * DAFTAR AKUN BARU
     *
     * Endpoint: POST /api/auth/register
     *
     * PERHATIKAN:
     * - Sebelum: public function register(Request $request)
     * - Sesudah: public function register(RegisterRequest $request)
     *
     * Dengan pakai RegisterRequest:
     * - Validasi otomatis dijalankan SEBELUM masuk ke function ini
     * - Jika validasi gagal, langsung return error 422
     * - Kita tidak perlu tulis $request->validate([...]) lagi
     *
     * Lihat validasinya di: app/Http/Requests/RegisterRequest.php
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // $request->validated() = ambil data yang sudah lolos validasi
            // Data yang tidak ada di rules() akan dibuang
            $validated = $request->validated();

            // Buat user baru di database
            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),  // Enkripsi password
                'role'       => $validated['role'] ?? 'student',  // Default role = student
                'phone'      => $validated['phone'] ?? null,
                'gender'     => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
            ]);

            // Kirim welcome email via queue (background)
            SendWelcomeEmail::dispatch($user);

            // Berikan subscription gratis otomatis untuk akun baru
            try {
                $now = Carbon::now();
                Subscription::create([
                    'user_id' => $user->id,
                    'plan' => 'free',
                    'status' => 'active',
                    'start_date' => $now,
                    // set end_date jauh di masa depan agar dianggap aktif oleh cek durasi
                    'end_date' => $now->copy()->addYears(100),
                    'package_type' => 'all_in_one',
                    'duration' => 100,
                    'duration_unit' => 'years',
                    'price' => 0,
                    'auto_renew' => false,
                ]);
            } catch (\Exception $e) {
                // Log error namun jangan gagalkan pendaftaran
                \Illuminate\Support\Facades\Log::error('Failed to assign free subscription on register', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Return response dengan UserResource
            // UserResource akan format data user secara konsisten
            return $this->createdResponse(
                new UserResource($user),
                'Pendaftaran berhasil. Silakan login untuk melanjutkan.'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Pendaftaran gagal', $e->getMessage());
        }
    }

    /**
     * LOGIN / MASUK
     *
     * Endpoint: POST /api/auth/login
     *
     * PERHATIKAN:
     * - Pakai LoginRequest untuk validasi
     * - Validasinya ada di: app/Http/Requests/LoginRequest.php
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Data sudah divalidasi oleh LoginRequest
            // Cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            // Cek apakah user ada dan password benar
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->unauthorizedResponse('Email atau password salah');
            }

            // Buat token JWT untuk user
            $token = JWTAuth::fromUser($user);
            $ttl = config('jwt.ttl', 60);  // Waktu expired (menit)

            // Return data user dan token
            return $this->successResponse([
                'user'       => new UserResource($user),  // Pakai UserResource
                'token'      => $token,
                'token_type' => 'Bearer',
                'expires_in' => $ttl * 60,  // Dalam detik
            ], 'Login berhasil');

        } catch (JWTException $e) {
            return $this->serverErrorResponse('Gagal membuat token', $e->getMessage());
        }
    }

    /**
     * LOGOUT / KELUAR
     *
     * Endpoint: POST /api/auth/logout
     *
     * Header: Authorization: Bearer {token}
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'Logout berhasil');
        } catch (JWTException $e) {
            return $this->serverErrorResponse('Gagal logout');
        }
    }

    /**
     * PERBARUI TOKEN
     *
     * Endpoint: POST /api/auth/refresh
     *
     * Gunakan ketika token hampir expired
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            $ttl = config('jwt.ttl', 60);

            return $this->successResponse([
                'token'      => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => $ttl * 60,
            ], 'Token berhasil diperbarui');

        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Gagal memperbarui token');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 2: PROFIL PENGGUNA
    |--------------------------------------------------------------------------
    */

    /**
     * LIHAT DATA SAYA (User yang sedang login)
     *
     * Endpoint: GET /api/auth/me
     *
     * PERHATIKAN: Pakai UserResource untuk format output
     */
    public function me(Request $request): JsonResponse
    {
        // new UserResource($user) akan format data sesuai yang ada di UserResource
        return $this->successResponse(
            new UserResource($request->user()),
            'Data pengguna berhasil diambil'
        );
    }

    /**
     * LIHAT PROFIL LENGKAP (dengan achievement, pengalaman, dll)
     *
     * Endpoint: GET /api/auth/profile
     */
    public function profile(Request $request): JsonResponse
    {
        // Load user beserta relasi-relasinya
        $user = $request->user()->load(['achievements', 'experiences', 'subscriptions']);

        return $this->successResponse([
            'user' => new UserResource($user),
            'achievements' => $user->achievements,
            'experiences' => $user->experiences,
            'subscriptions' => $user->subscriptions,
        ], 'Profil berhasil diambil');
    }

    /**
     * UPDATE PROFIL
     *
     * Endpoint: PUT /api/auth/profile
     *
     * Validasi di: app/Http/Requests/UpdateProfileRequest.php
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        // $request->validated() = data yang sudah lolos validasi
        $user = $request->user();
        $user->update($request->validated());

        return $this->successResponse(
            new UserResource($user->fresh()),
            'Profil berhasil diupdate'
        );
    }

    /**
     * GANTI PASSWORD
     *
     * Endpoint: PUT /api/auth/change-password
     *
     * PERHATIKAN:
     * - Pakai ChangePasswordRequest untuk validasi
     * - Validasinya ada di: app/Http/Requests/ChangePasswordRequest.php
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        // Data sudah divalidasi oleh ChangePasswordRequest
        $user = $request->user();

        // Cek apakah password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->unauthorizedResponse('Password lama tidak sesuai');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return $this->successResponse(null, 'Password berhasil diubah');
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 3: UPLOAD FILE
    |--------------------------------------------------------------------------
    */

    /**
     * UPLOAD FOTO PROFIL
     *
     * Endpoint: POST /api/auth/profile/photo
     *
     * Data: photo (jpeg, png, jpg, gif) max 2MB
     */
    public function uploadProfilePhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $blocked = ['php', 'phtml', 'phar', 'cgi', 'pl', 'exe', 'js', 'sh'];
                    if (in_array(strtolower($value->getClientOriginalExtension()), $blocked, true)) {
                        $fail('File tidak diizinkan.');
                    }
                },
            ],
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
            $user->save();
        }

        return $this->successResponse([
            'profile_photo'     => $user->profile_photo,
            'profile_photo_url' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
        ], 'Foto profil berhasil diupload');
    }

    /**
     * UPLOAD CV (Curriculum Vitae)
     *
     * Endpoint: POST /api/auth/profile/cv
     *
     * Data: cv (pdf, doc, docx) max 2MB
     */
    public function uploadCv(Request $request): JsonResponse
    {
        $request->validate([
            'cv' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $blocked = ['php', 'phtml', 'phar', 'cgi', 'pl', 'exe', 'js', 'sh'];
                    if (in_array(strtolower($value->getClientOriginalExtension()), $blocked, true)) {
                        $fail('File tidak diizinkan.');
                    }
                },
            ],
        ]);

        $user = $request->user();

        if ($request->hasFile('cv')) {
            // Hapus CV lama
            if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
                Storage::disk('public')->delete($user->cv_path);
            }

            // Simpan CV baru
            $path = $request->file('cv')->store('cvs', 'public');
            $user->cv_path = $path;
            $user->save();
        }

        return $this->successResponse([
            'cv_path' => $user->cv_path,
            'cv_url'  => $user->cv_path ? asset('storage/' . $user->cv_path) : null,
        ], 'CV berhasil diupload');
    }

    /**
     * GET CV (Curriculum Vitae)
     *
     * Endpoint: GET /api/auth/profile/cv
     *
     * Menampilkan informasi CV atau download CV user
     */
    public function getCv(Request $request)
    {
        $user = $request->user();

        if (!$user->cv_path) {
            return $this->errorResponse('CV tidak ditemukan', 404);
        }

        if (!Storage::disk('public')->exists($user->cv_path)) {
            return $this->errorResponse('File CV tidak ditemukan', 404);
        }

        // Jika parameter download=true, return file untuk di-download
        if ($request->query('download') === 'true') {
            return Storage::disk('public')->download($user->cv_path);
        }

        // Jika tidak, return informasi CV
        return $this->successResponse([
            'cv_path' => $user->cv_path,
            'cv_url'  => asset('storage/' . $user->cv_path),
            'cv_name' => basename($user->cv_path),
        ], 'CV berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 4: DASHBOARD & PORTOFOLIO
    |--------------------------------------------------------------------------
    */

    /**
     * REKOMENDASI KURSUS
     *
     * Endpoint: GET /api/auth/recommendations
     * 
     * Algoritma rekomendasi berdasarkan:
     * 1. Subscription plan user (hanya tampilkan course yang bisa diakses)
     * 2. Course yang belum di-enroll
     * 3. Relevansi dengan specialization/minat (prioritas tertinggi)
     * 4. Relevansi dengan major/jurusan
     * 5. Popularity (jumlah enrollment)
     * 6. Rating tertinggi
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->input('limit', 5);
        
        // Get user's active subscription to determine accessible courses
        $subscription = $user->activeSubscription();
        $plan = $subscription ? $subscription->plan : 'free';
        
        // Get courses user is already enrolled in
        $enrolledCourseIds = $user->enrollments()->pluck('course_id')->toArray();
        
        // Build base query
        $query = \App\Models\Course::query()
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->whereNotIn('id', $enrolledCourseIds); // Exclude already enrolled courses
        
        // Filter by accessible courses based on subscription plan
        if ($plan === 'free') {
            $query->where('access_type', 'free');
        } elseif ($plan === 'regular') {
            $query->whereIn('access_type', ['free', 'regular']);
        }
        // Premium users can see all courses
        
        // Get user's specialization (minat) and major for recommendation
        $specializations = $user->specialization ?? [];
        $major = $user->major;
        
        // Score-based recommendation using SELECT with scoring
        if (!empty($specializations) || $major) {
            // Build dynamic CASE WHEN for specializations and major
            $caseClauses = [];
            $bindings = [];
            
            // Specialization matching (highest priority: 150-80 points)
            if (!empty($specializations)) {
                foreach ($specializations as $index => $spec) {
                    $specLower = strtolower($spec);
                    // Exact match in title gets highest score
                    $caseClauses[] = "WHEN LOWER(title) LIKE ? THEN " . (150 - ($index * 10));
                    $bindings[] = '%' . $specLower . '%';
                    // Match in category gets medium score
                    $caseClauses[] = "WHEN LOWER(category) LIKE ? THEN " . (100 - ($index * 10));
                    $bindings[] = '%' . $specLower . '%';
                    // Match in description gets lower score
                    $caseClauses[] = "WHEN LOWER(description) LIKE ? THEN " . (80 - ($index * 10));
                    $bindings[] = '%' . $specLower . '%';
                }
            }
            
            // Major matching (medium priority: 60-30 points)
            if ($major) {
                $majorLower = strtolower($major);
                $caseClauses[] = "WHEN LOWER(title) LIKE ? THEN 60";
                $bindings[] = '%' . $majorLower . '%';
                $caseClauses[] = "WHEN LOWER(description) LIKE ? THEN 40";
                $bindings[] = '%' . $majorLower . '%';
                $caseClauses[] = "WHEN LOWER(category) LIKE ? THEN 30";
                $bindings[] = '%' . $majorLower . '%';
            }
            
            $caseClauses[] = "ELSE 0";
            $caseStatement = implode(" ", $caseClauses);
            
            $query->selectRaw("
                courses.*,
                (
                    CASE 
                        {$caseStatement}
                    END
                ) as relevance_score
            ", $bindings)
            ->orderByDesc('relevance_score')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('enrollments_count');
        } else {
            // No specialization or major: sort by rating and popularity
            $query->orderByDesc('reviews_avg_rating')
                  ->orderByDesc('enrollments_count');
        }
        
        $recommendations = $query->limit($limit)->get();
        
        // If no recommendations found (e.g., all courses enrolled), show top-rated accessible courses
        if ($recommendations->isEmpty()) {
            $fallbackQuery = \App\Models\Course::query()
                ->withCount('enrollments')
                ->withAvg('reviews', 'rating');
                
            if ($plan === 'free') {
                $fallbackQuery->where('access_type', 'free');
            } elseif ($plan === 'regular') {
                $fallbackQuery->whereIn('access_type', ['free', 'regular']);
            }
            
            $recommendations = $fallbackQuery
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('enrollments_count')
                ->limit($limit)
                ->get();
        }
        
        return $this->successResponse([
            'recommendations' => $recommendations,
            'criteria' => [
                'subscription_plan' => $plan,
                'specializations' => $specializations ?? [],
                'major' => $user->major ?? 'not_specified',
                'excluded_enrolled' => count($enrolledCourseIds),
                'algorithm' => 'specialization_score + major_score + rating + popularity',
            ],
        ], 'Rekomendasi kursus berhasil diambil');
    }

    /**
     * LIHAT PORTOFOLIO LENGKAP
     *
     * Endpoint: GET /api/auth/portfolio
     */
    public function portfolio(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'achievements',
            'experiences',
            'organizations',
            'enrollments.course',
            'scholarshipApplications.scholarship',
            'mentoringSessionsAsStudent',
            'mentoringSessionsAsMentor',
            'subscriptions',
        ]);

        return $this->successResponse([
            'profile'            => new UserResource($user),  // Pakai UserResource
            'prestasi'           => $user->achievements,
            'pengalaman'         => $user->experiences,
            'organisasi'         => $user->organizations,
            'kursus'             => $user->enrollments,
            'lamaran_beasiswa'   => $user->scholarshipApplications,
            'sesi_mentoring'     => [
                'sebagai_murid'  => $user->mentoringSessionsAsStudent,
                'sebagai_mentor' => $user->mentoringSessionsAsMentor,
            ],
            'langganan' => $user->subscriptions,
        ], 'Portofolio berhasil diambil');
    }

    /**
     * RIWAYAT AKTIVITAS
     *
     * Endpoint: GET /api/auth/activity-history
     */
    public function activityHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        $ringkasan = [
            'kursus_selesai'        => $user->enrollments()->where('completed', true)->count(),
            'kursus_sedang_diambil' => $user->enrollments()->where('completed', false)->count(),
            'mentoring_selesai'     => $user->mentoringSessionsAsStudent()->where('status', 'completed')->count(),
            'lamaran_beasiswa'      => $user->scholarshipApplications()->count(),
            'jumlah_prestasi'       => $user->achievements()->count(),
            'jumlah_pengalaman'     => $user->experiences()->count(),
            'jumlah_organisasi'     => $user->organizations()->count(),
        ];

        $terbaru = [
            'kursus_terbaru'    => $user->enrollments()->with('course')->latest()->limit(5)->get(),
            'lamaran_terbaru'   => $user->scholarshipApplications()->with('scholarship')->latest()->limit(5)->get(),
            'mentoring_terbaru' => $user->mentoringSessionsAsStudent()->with('mentor')->latest()->limit(5)->get(),
        ];

        return $this->successResponse([
            'ringkasan' => $ringkasan,
            'terbaru'   => $terbaru,
        ], 'Riwayat aktivitas berhasil diambil');
    }
}
