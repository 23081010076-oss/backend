<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine if the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        return true;
    }

    /**
     * Determine if the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin');
    }
}
