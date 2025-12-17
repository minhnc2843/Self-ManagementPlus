<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UpcomingEventNotification extends Notification
{
    use Queueable;

    public function __construct(public Event $event) {}

    public function via($notifiable)
    {
        return ['database','broadcat']; // lưu DB để hiển thị trang thông báo
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'        => 'upcoming_event',
            'event_id'    => $this->event->id,
            'title'       => $this->event->title,
            'start_time'  => $this->event->start_time->format('d/m/Y H:i'),
            'message'     => "Bạn có lịch hẹn sắp tới: {$this->event->title}",
            'url'         => route('events.edit', $this->event->id),
        ];
    }
    public function toBroadcast($notifiable)
{
    return new BroadcastMessage([
        'type' => 'upcoming_event',
        'title' => 'Lịch hẹn sắp tới',
        'message' => "Bạn có lịch hẹn sắp tới: {$this->event->title}",
        'event_id' => $this->event->id,
        'url' => route('events.edit', $this->event->id),
        'time' => now()->toDateTimeString(),
    ]);
}
}
