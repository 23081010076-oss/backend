<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'gender',
        'birth_date',
        'phone',
        'address',
        'institution',
        'major',
        'education_level',
        'bio',
        'profile_photo',
        'cv_path',
        'google_id',
        'avatar',
        'specialization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'specialization' => 'array',
        ];
    }

    // Relationships
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scholarshipApplications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    public function mentoringSessionsAsMentor()
    {
        return $this->hasMany(MentoringSession::class, 'mentor_id');
    }

    public function mentoringSessionsAsStudent()
    {
        return $this->hasMany(MentoringSession::class, 'member_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Get user's active subscription
     * 
     * @return \App\Models\Subscription|null
     */
    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->latest()->first();
    }

    /**
     * Check if user has active subscription of specific plan
     * 
     * @param string $plan (free, regular, premium)
     * @return bool
     */
    public function hasActivePlan(string $plan): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->plan === $plan;
    }

    /**
     * Check if user can access course based on subscription
     * 
     * @param string $accessType (free, regular, premium)
     * @return bool
     */
    public function canAccessCourseType(string $accessType): bool
    {
        if ($accessType === 'free') {
            return true;
        }

        $subscription = $this->activeSubscription();
        
        if (!$subscription) {
            return false;
        }

        if ($accessType === 'premium') {
            return $subscription->plan === 'premium';
        }

        if ($accessType === 'regular') {
            return in_array($subscription->plan, ['regular', 'premium']);
        }

        return false;
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
