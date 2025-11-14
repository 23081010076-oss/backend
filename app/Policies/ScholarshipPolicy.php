<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Scholarship;
use Illuminate\Auth\Access\Response;

class ScholarshipPolicy
{
    /**
     * Determine if the user can view any scholarships.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the scholarship.
     */
    public function view(User $user, Scholarship $scholarship): bool
    {
        return true;
    }

    /**
     * Determine if the user can create scholarships.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('corporate');
    }

    /**
     * Determine if the user can update the scholarship.
     */
    public function update(User $user, Scholarship $scholarship): bool
    {
        return $user->hasRole('corporate') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the scholarship.
     */
    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $user->hasRole('corporate') || $user->hasRole('admin');
    }
}
