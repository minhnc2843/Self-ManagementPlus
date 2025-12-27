<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'start_time', 'end_time', 'is_priority', 'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_priority' => 'boolean',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}