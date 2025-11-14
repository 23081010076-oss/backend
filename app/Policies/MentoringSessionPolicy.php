<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MentoringSession;

class MentoringSessionPolicy
{
    /**
     * Determine if the user can view any mentoring sessions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the mentoring session.
     */
    public function view(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->mentor_id || $user->id === $session->member_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create mentoring sessions.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['student', 'mentor']);
    }

    /**
     * Determine if the user can update the mentoring session.
     */
    public function update(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->mentor_id || $user->id === $session->member_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the mentoring session.
     */
    public function delete(User $user, MentoringSession $session): bool
    {
        return $user->id === $session->mentor_id || $user->id === $session->member_id || $user->hasRole('admin');
    }
}
