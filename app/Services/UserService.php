<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * ==========================================================================
 * USER SERVICE (Service untuk Manajemen User)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen user.
 * 
 * KENAPA PAKAI SERVICE?
 * - Logika hash password, status management terpusat
 * - Controller tetap ringkas
 * - Mudah dipakai di berbagai tempat
 */
class UserService
{
    /**
     * Ambil daftar user dengan filter
     */
    public function getUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        // Filter berdasarkan role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Pencarian berdasarkan nama atau email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Ambil detail user dengan relasi
     */
    public function getUserWithDetails(int $id): User
    {
        return User::with([
            'achievements',
            'experiences',
            'enrollments.course',
        ])->findOrFail($id);
    }

    /**
     * Buat user baru
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        // Hash password jika disediakan
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Hapus user
     * 
     * @throws \Exception jika menghapus diri sendiri
     */
    public function deleteUser(User $user, int $currentUserId): bool
    {
        if ($user->id === $currentUserId) {
            throw new \Exception('Anda tidak bisa menghapus akun sendiri');
        }

        return $user->delete();
    }

    /**
     * Update status user
     */
    public function updateStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);
        
        return $user->fresh();
    }

    /**
     * Suspend user
     * 
     * @throws \Exception jika suspend diri sendiri
     */
    public function suspendUser(User $user, int $currentUserId): User
    {
        if ($user->id === $currentUserId) {
            throw new \Exception('Anda tidak bisa menangguhkan akun sendiri');
        }

        return $this->updateStatus($user, 'suspended');
    }

    /**
     * Aktifkan user
     */
    public function activateUser(User $user): User
    {
        return $this->updateStatus($user, 'active');
    }

    /**
     * Ambil daftar mentor
     */
    public function getMentors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::where('role', 'mentor')
            ->where('status', 'active');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Ambil statistik user
     */
    public function getStatistics(): array
    {
        return [
            'total'     => User::count(),
            'admins'    => User::where('role', 'admin')->count(),
            'students'  => User::where('role', 'student')->count(),
            'mentors'   => User::where('role', 'mentor')->count(),
            'corporate' => User::where('role', 'corporate')->count(),
            'active'    => User::where('status', 'active')->count(),
            'inactive'  => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];
    }

    /**
     * Cari user berdasarkan email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
