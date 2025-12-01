<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EnrollmentPolicy
 * 
 * Authorization policy for Enrollment resource.
 * Defines who can view, create, update, and delete enrollments.
 * 
 * @package App\Policies
 */
class EnrollmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any enrollments
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view enrollments
        return true;
    }

    /**
     * Determine whether the user can view the enrollment
     *
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // User can view their own enrollment or admin can view all
        return $user->id === $enrollment->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create enrollments
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can enroll in courses
        return true;
    }

    /**
     * Determine whether the user can update the enrollment
     *
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // User can update their own enrollment or admin can update all
        return $user->id === $enrollment->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the enrollment
     *
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        // User can delete their own enrollment or admin can delete all
        return $user->id === $enrollment->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update progress
     *
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function updateProgress(User $user, Enrollment $enrollment): bool
    {
        // Only the enrolled user can update their own progress
        return $user->id === $enrollment->user_id;
    }
}
