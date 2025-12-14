<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_type',
        'location',
        'created_by',
        'start_time',
        'end_time',
        'repeat_rule',
        'repeat_meta',
        'priority',
        'is_important',
        'status',
    ];

    protected $casts = [
        'repeat_meta' => 'array',
        'is_important' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function reminders()
    {
        return $this->hasMany(EventReminder::class);
    }

    public function histories()
    {
        return $this->hasMany(EventHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
