<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_code',
        'type',
        'transactionable_id',
        'transactionable_type',
        'amount',
        'payment_method',
        'status',
        'payment_details',
        'payment_proof',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'payment_details' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship
    public function transactionable()
    {
        return $this->morphTo();
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || ($this->expired_at && $this->expired_at->isPast());
    }

    // Generate unique transaction code
    public static function generateTransactionCode()
    {
        do {
            $code = 'TRX' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }
}
