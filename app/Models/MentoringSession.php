<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MentoringSession extends Model
{
    use HasFactory;

    protected $table = 'mentoring_sessions';

    protected $fillable = [
        'mentor_id',
        'member_id',
        'session_id',
        'type',
        'schedule',
        'meeting_link',
        'payment_method',
        'status',
        'need_assessment_status',
        'assessment_form_data',
        'coaching_files_path',
    ];

    protected $casts = [
        'schedule' => 'datetime',
    ];

    // Relationships
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function needAssessment()
    {
        return $this->hasOne(NeedAssessment::class);
    }

    public function coachingFiles()
    {
        return $this->hasMany(CoachingFile::class);
    }
}
