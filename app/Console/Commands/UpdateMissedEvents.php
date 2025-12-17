<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventHistory;
use Carbon\Carbon;

class UpdateMissedEvents extends Command
{
    protected $signature = 'events:update-missed';
    protected $description = 'Mark past events as missed';

    public function handle()
    {
        $now = Carbon::now();

        $events = Event::where('status', 'upcoming')
            ->where('end_time', '<', $now)
            ->get();

        foreach ($events as $event) {
            $oldStatus = $event->status;

            $event->update([
                'status' => 'missed',
            ]);

            EventHistory::create([
                'event_id' => $event->id,
                'user_id'  => $event->created_by,
                'action'   => 'auto_missed',
                'old_data' => ['status' => $oldStatus],
                'new_data' => ['status' => 'missed'],
            ]);
        }

        return Command::SUCCESS;
    }
}
