<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',             // income, expense
        'category',         // Tên hạng mục (Ăn sáng, Xăng xe...)
        'amount',
        'transaction_date',
        'description'
    ];

    /**
     * Định dạng kiểu dữ liệu
     */
    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Mỗi giao dịch thuộc về 1 User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Helper để hiển thị tên loại biến động bằng tiếng Việt
     * Cách dùng: $transaction->type_label
     */
    public function getTypeLabelAttribute()
    {
        return $this->type === 'income' ? 'Thu nhập' : 'Chi tiêu';
    }
    
    /**
     * Accessor: Helper để hiển thị màu sắc (cho tailwind sau này)
     */
    public function getTypeColorAttribute()
    {
        return $this->type === 'income' ? 'text-green-500' : 'text-red-500';
    }
}