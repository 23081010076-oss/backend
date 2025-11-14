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
}
