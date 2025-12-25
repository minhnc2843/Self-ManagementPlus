<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_group_id',
        'payer_id', // Người trả nợ
        'payee_id', // Người nhận nợ
        'amount',
        'date',
        'status',   // pending, completed, rejected
        'note'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Quan hệ về Nhóm
    public function group()
    {
        return $this->belongsTo(ExpenseGroup::class, 'expense_group_id');
    }

    // Người trả (Debtor)
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    // Người nhận (Creditor)
    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }
}