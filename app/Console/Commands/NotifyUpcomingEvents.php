<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use App\Notifications\UpcomingEventNotification;

class NotifyUpcomingEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
   public function handle()
{
    $now = now();
    $soon = now()->addHour();

    $events = Event::whereBetween('start_time', [$now, $soon])
        ->where('status', 'upcoming')
        ->get();

    foreach ($events as $event) {
        $user = $event->creator;

        if (!$user->notifications()
            ->where('data->event_id', $event->id)
            ->where('data->type', 'upcoming_event')
            ->exists()
        ) {
            $user->notify(new UpcomingEventNotification($event));
        }
    }
}

}
