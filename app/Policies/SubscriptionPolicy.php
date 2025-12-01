<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SubscriptionPolicy
 * 
 * Authorization policy for Subscription resource.
 * Defines who can view, create, update, and delete subscriptions.
 * 
 * @package App\Policies
 */
class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any subscriptions
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view their own subscriptions
        return true;
    }

    /**
     * Determine whether the user can view the subscription
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function view(User $user, Subscription $subscription): bool
    {
        // User can view their own subscription or admin can view all
        return $user->id === $subscription->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create subscriptions
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can create subscriptions
        return true;
    }

    /**
     * Determine whether the user can update the subscription
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function update(User $user, Subscription $subscription): bool
    {
        // User can update their own subscription or admin can update all
        return $user->id === $subscription->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the subscription
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        // User can delete their own subscription or admin can delete all
        return $user->id === $subscription->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can upgrade the subscription
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function upgrade(User $user, Subscription $subscription): bool
    {
        // Only the owner can upgrade their subscription
        return $user->id === $subscription->user_id;
    }
}
