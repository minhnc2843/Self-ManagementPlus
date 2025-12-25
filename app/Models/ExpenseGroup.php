<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'creator_id'];

    // Lấy thành viên
    public function members()
    {
        return $this->belongsToMany(User::class, 'expense_group_members', 'expense_group_id', 'user_id');
    }

    // Lấy các khoản chi trong nhóm
    public function expenses()
    {
        return $this->hasMany(SharedExpense::class);
    }
}