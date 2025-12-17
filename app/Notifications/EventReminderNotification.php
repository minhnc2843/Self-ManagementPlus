<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Event;

class EventReminderNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    // Ghi vào database
        public function toDatabase($notifiable)
    {
        return [
            'title'        => 'Nhắc nhở sự kiện: ' . $this->event->title,
            'message'      => 'Sự kiện sắp diễn ra vào lúc ' . \Carbon\Carbon::parse($this->event->start_time)->format('H:i d/m/Y'),
            'url'          => route('events.edit', $this->event->id), // Link đến trang chi tiết/sửa
            'icon'         => 'heroicons-outline:calendar', // Icon dùng cho Dashcode (Iconify)
            'color'        => 'warning', // Màu sắc: warning, success, danger, info
            'type'         => 'event_reminder', // Để phân loại hiển thị nếu cần
        ];
    }

    // Gửi realtime qua broadcast (Pusher)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'event_id'     => $this->event->id,
            'title'        => $this->event->title,
            'start_time'   => $this->event->start_time,
            'priority'     => $this->event->priority,
            'is_important' => $this->event->is_important,
        ]);
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function broadcastType()
    {
        return 'event.reminder';
    }

    public function broadcastOn()
    {
        return ['private-user-' . $this->event->created_by];
    }
}
