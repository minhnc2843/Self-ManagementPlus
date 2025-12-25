<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedExpense extends Model
{
    use HasFactory;

    // Cập nhật fillable theo cấu trúc bảng mới
    protected $fillable = [
        'expense_group_id',
        'created_by',    // Người tạo đơn (nhập liệu)
        'title',
        'total_amount',  // Tổng tiền hóa đơn
        'date',
        'description'
    ];

    protected $casts = [
        'date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Người tạo (nhập liệu)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Những người đã trả tiền (Payers)
    public function payers()
    {
        return $this->hasMany(ExpensePayer::class);
    }

    // Những người phải chia tiền (Shares)
    public function shares()
    {
        return $this->hasMany(ExpenseShare::class);
    }
}