<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ReviewPolicy
 * 
 * Authorization policy for Review resource.
 * Defines who can view, create, update, and delete reviews.
 * 
 * @package App\Policies
 */
class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reviews
     *
     * @param User|null $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view reviews (even guests)
        return true;
    }

    /**
     * Determine whether the user can view the review
     *
     * @param User|null $user
     * @param Review $review
     * @return bool
     */
    public function view(?User $user, Review $review): bool
    {
        // Anyone can view individual reviews
        return true;
    }

    /**
     * Determine whether the user can create reviews
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can create reviews
        return true;
    }

    /**
     * Determine whether the user can update the review
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function update(User $user, Review $review): bool
    {
        // User can only update their own review
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete the review
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function delete(User $user, Review $review): bool
    {
        // User can delete their own review or admin can delete all
        return $user->id === $review->user_id || $user->role === 'admin';
    }
}
