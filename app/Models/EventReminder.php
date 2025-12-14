<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventReminder extends Model
{
    protected $fillable = [
        'event_id',
        'remind_at',
        'is_sent'
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_sent' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
