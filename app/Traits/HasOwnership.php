<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait HasOwnership
 * 
 * Provides reusable methods for checking resource ownership.
 * Helps prevent code duplication in controllers and policies.
 * 
 * @package App\Traits
 */
trait HasOwnership
{
    /**
     * Check if the authenticated user owns the resource
     *
     * @param Model $resource Resource to check ownership
     * @param string $ownerColumn Column name for owner ID (default: 'user_id')
     * @return bool
     */
    protected function isOwner(Model $resource, string $ownerColumn = 'user_id'): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $resource->{$ownerColumn} === $user->id;
    }

    /**
     * Check if the authenticated user is the owner or an admin
     *
     * @param Model $resource Resource to check ownership
     * @param string $ownerColumn Column name for owner ID (default: 'user_id')
     * @return bool
     */
    protected function isOwnerOrAdmin(Model $resource, string $ownerColumn = 'user_id'): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $this->isAdmin($user) || $resource->{$ownerColumn} === $user->id;
    }

    /**
     * Check if the user is an admin
     *
     * @param User|null $user User to check (defaults to authenticated user)
     * @return bool
     */
    protected function isAdmin(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->role === 'admin';
    }

    /**
     * Check if the user has one of the specified roles
     *
     * @param array $roles Array of role names
     * @param User|null $user User to check (defaults to authenticated user)
     * @return bool
     */
    protected function hasRole(array $roles, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return in_array($user->role, $roles);
    }

    /**
     * Authorize that the user owns the resource or is an admin
     * Throws exception if not authorized
     *
     * @param Model $resource Resource to check ownership
     * @param string $ownerColumn Column name for owner ID (default: 'user_id')
     * @param string $message Custom error message
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return void
     */
    protected function authorizeOwnership(Model $resource, string $ownerColumn = 'user_id', string $message = 'Unauthorized'): void
    {
        if (!$this->isOwnerOrAdmin($resource, $ownerColumn)) {
            abort(403, $message);
        }
    }

    /**
     * Get query scope to filter resources by ownership
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User|null $user User to filter by (defaults to authenticated user)
     * @param string $ownerColumn Column name for owner ID (default: 'user_id')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeOwnedBy($query, ?User $user = null, string $ownerColumn = 'user_id')
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        // Admin can see all
        if ($this->isAdmin($user)) {
            return $query;
        }

        return $query->where($ownerColumn, $user->id);
    }
}
