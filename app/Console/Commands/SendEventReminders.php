<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventReminder;
use App\Notifications\UpcomingEventNotification;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send event reminders to users';

    public function handle()
    {
        $now = Carbon::now();

        $reminders = EventReminder::with('event')
            ->where('is_sent', false)
            ->where('remind_at', '<=', $now)
            ->get();

        foreach ($reminders as $reminder) {
            $event = $reminder->event;

            if (!$event || $event->status !== 'upcoming') {
                $reminder->update(['is_sent' => true]);
                continue;
            }

            $user = $event->creator; // quan hệ created_by → User

            if ($user) {
                $user->notify(new UpcomingEventNotification($event));
            }

            $reminder->update([
                'is_sent' => true,
            ]);
        }

        return Command::SUCCESS;
    }
}
