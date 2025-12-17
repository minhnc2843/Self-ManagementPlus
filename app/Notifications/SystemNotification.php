<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SystemNotification extends Notification
{
    use Queueable;

    protected $action;  // Ví dụ: Sự kiện mới, Cập nhật, Cảnh báo...
    protected $title;   // Ví dụ: Tên sự kiện (Họp Team Marketing)
    protected $message; // Nội dung chi tiết
    protected $url;
    protected $type;    // success, warning, danger, info (để chọn màu)

    /**
     * Create a new notification instance.
     */
    public function __construct($action, $title, $message, $url = '#', $type = 'info')
    {
        $this->action = $action;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database']; // Thêm 'broadcast' nếu cần realtime
    }

    public function toDatabase($notifiable)
    {
        return [
            'action'  => $this->action,  // Loại thông báo (Mới thêm vào)
            'title'   => $this->title,   // Tiêu đề chính
            'message' => $this->message, // Sửa key 'content' thành 'message' cho thống nhất với View
            'url'     => $this->url,
            'icon'    => $this->getIcon($this->type), // Lấy icon tự động theo type
            'color'   => $this->type,
        ];
    }

    // Hàm phụ trợ để chọn icon dựa trên loại thông báo
    protected function getIcon($type)
    {
        return match($type) {
            'success' => 'heroicons-outline:check-circle',
            'warning' => 'heroicons-outline:exclamation',
            'danger'  => 'heroicons-outline:trash',
            default   => 'heroicons-outline:information-circle',
        };
    }
}