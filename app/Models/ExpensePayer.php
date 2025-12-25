<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_expense_id',
        'user_id',
        'amount_paid'
    ];

    // Quan hệ ngược về hóa đơn tổng
    public function expense()
    {
        return $this->belongsTo(SharedExpense::class, 'shared_expense_id');
    }

    // Quan hệ về User (Người trả tiền)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}