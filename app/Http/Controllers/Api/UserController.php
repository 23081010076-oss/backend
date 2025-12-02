<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Request Classes
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

/**
 * ==========================================================================
 * USER CONTROLLER (Controller untuk Manajemen User)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola user oleh admin.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/UserService.php
 * - Policy      : Authorization  → app/Policies/UserPolicy.php
 * - Request     : Validation     → app/Http/Requests/User/
 */
class UserController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected UserService $userService;

    /**
     * Constructor - Inject service
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar user dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getUsers($request->all());

        return $this->paginatedResponse($users, 'Daftar user berhasil diambil');
    }

    /**
     * Tampilkan detail user
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('view', $user);

        $userWithDetails = $this->userService->getUserWithDetails($id);

        return $this->successResponse($userWithDetails, 'Detail user berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah user baru (admin only)
     * 
     * Validasi di: app/Http/Requests/User/StoreUserRequest.php
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', User::class);

        $user = $this->userService->createUser($request->validated());

        return $this->createdResponse($user, 'User berhasil ditambahkan');
    }

    /**
     * Update user (admin only)
     * 
     * Validasi di: app/Http/Requests/User/UpdateUserRequest.php
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $user);

        $user = $this->userService->updateUser($user, $request->validated());

        return $this->successResponse($user, 'User berhasil diupdate');
    }

    /**
     * Hapus user (admin only)
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($user, Auth::id());
            return $this->successResponse(null, 'User berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Status Management Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update status user (admin only)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $user);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ], [
            'status.required' => 'Status harus diisi',
            'status.in'       => 'Status harus salah satu dari: active, inactive, suspended',
        ]);

        $user = $this->userService->updateStatus($user, $validated['status']);

        return $this->successResponse($user, 'Status user berhasil diupdate');
    }

    /**
     * Suspend akun user
     */
    public function suspend(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('suspend', $user);

        try {
            $user = $this->userService->suspendUser($user, Auth::id());
            return $this->successResponse($user, 'User berhasil ditangguhkan');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Aktifkan akun user yang ditangguhkan
     */
    public function activate(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('activate', $user);

        $user = $this->userService->activateUser($user);

        return $this->successResponse($user, 'User berhasil diaktifkan');
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics & Reports
    |--------------------------------------------------------------------------
    */

    /**
     * Statistik user
     */
    public function statistics(): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('viewStatistics', User::class);

        $stats = $this->userService->getStatistics();

        return $this->successResponse($stats, 'Statistik user berhasil diambil');
    }

    /**
     * Daftar mentor
     */
    public function mentors(Request $request): JsonResponse
    {
        $mentors = $this->userService->getMentors($request->all());

        return $this->paginatedResponse($mentors, 'Daftar mentor berhasil diambil');
    }
}
