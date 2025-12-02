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
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
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

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 4: DASHBOARD & PORTOFOLIO
    |--------------------------------------------------------------------------
    */

    /**
     * REKOMENDASI KURSUS
     *
     * Endpoint: GET /api/auth/recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();
        $major = $user->major;

        $recommendedCourses = \App\Models\Course::query();

        if ($major) {
            $recommendedCourses->where(function ($query) use ($major) {
                $query->where('title', 'like', '%' . $major . '%')
                      ->orWhere('description', 'like', '%' . $major . '%');
            });
            $recommendations = $recommendedCourses->limit(5)->get();
        } else {
            $recommendations = collect();
        }

        if ($recommendations->isEmpty()) {
            $recommendations = \App\Models\Course::inRandomOrder()->limit(5)->get();
        }

        return $this->successResponse($recommendations, 'Rekomendasi kursus berhasil diambil');
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
