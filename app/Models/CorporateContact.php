<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorporateContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->nullable();
    }
}
