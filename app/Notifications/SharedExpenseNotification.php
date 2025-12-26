<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SharedExpenseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     * $data example: ['message' => 'Minh đã thêm bạn vào nhóm chi tiêu', 'url' => '/finance/shared/1']
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // 'database': lưu vào DB để hiện ở trang lịch sử
        // 'broadcast': đẩy realtime ra socket
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for Database.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->data['message'],
            'url' => $this->data['url'] ?? '#',
            'type' => 'shared_expense', // Để phân loại icon hiển thị bên frontend
            'created_at' => now(),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->data['message'],
            'url' => $this->data['url'] ?? '#',
            'type' => 'shared_expense',
            'created_at' => now(),
        ]);
    }
}