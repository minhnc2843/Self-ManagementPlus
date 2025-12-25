<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_expense_id',
        'user_id',
        'amount_owed' // Số tiền người này phải trả
    ];

    // Quan hệ với User (người chịu phí)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ ngược về Hóa đơn
    public function expense()
    {
        return $this->belongsTo(SharedExpense::class, 'shared_expense_id');
    }
}