<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'contact_name',
        'amount',
        'paid_amount',
        'interest_rate',
        'loan_date',
        'due_date',
        'status',
        'description',
        'completed_at'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper để tính số tiền còn lại
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }
}