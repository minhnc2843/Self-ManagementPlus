<?php

namespace App\Services;
use App\Models\Event;

use Carbon\Carbon;

class EventRepeatService
{
    public function getNextRepeatDate($event)
    {
        $start = Carbon::parse($event->start_time);

        switch ($event->repeat_rule) {

            case 'weekly':
                return $start->addWeek();

            case 'monthly':
                return $start->addMonth();

            case 'yearly':
                return $start->addYear();

            case 'custom':
                if (!isset($event->repeat_meta['weekday'])) return null;

                $weekdayList = $event->repeat_meta['weekday']; // [2,4]

                $next = $start->copy()->addDay();

                for ($i = 0; $i < 7; $i++) {
                    if (in_array($next->dayOfWeekIso, $weekdayList)) {
                        return $next;
                    }
                    $next->addDay();
                }

                return null;

            default:
                return null;
        }
    }

    public function createNextRepeatedEvent(Event $event): ?Event
    {
        if (empty($event->repeat_rule) || $event->repeat_rule === 'none') {
            return null;
        }

        $nextDate = $this->getNextRepeatDate($event);

        if (!$nextDate) {
            return null;
        }

        // Chỉ copy các thuộc tính cần thiết, không copy id, timestamps...
        $newEventData = $event->only([
            'title', 'description', 'event_type', 'location', 'created_by',
            'end_time', 'repeat_rule', 'repeat_meta', 'priority', 'is_important'
        ]);

        // Gán các giá trị mới
        $newEventData['start_time'] = $nextDate;
        $newEventData['status'] = 'upcoming'; // Sự kiện mới luôn là upcoming

        return Event::create($newEventData);
    }
}
