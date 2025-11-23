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
