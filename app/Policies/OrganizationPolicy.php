<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class OrganizationPolicy
 * 
 * Authorization policy for Organization resource.
 * Defines who can view, create, update, and delete organizations.
 * 
 * @package App\Policies
 */
class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view organizations
        return true;
    }

    /**
     * Determine whether the user can view the organization
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function view(User $user, Organization $organization): bool
    {
        // User can view their own organization or admin can view all
        return $user->id === $organization->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create organizations
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can create organizations
        return true;
    }

    /**
     * Determine whether the user can update the organization
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function update(User $user, Organization $organization): bool
    {
        // User can update their own organization or admin can update all
        return $user->id === $organization->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the organization
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function delete(User $user, Organization $organization): bool
    {
        // User can delete their own organization or admin can delete all
        return $user->id === $organization->user_id || $user->role === 'admin';
    }
}
