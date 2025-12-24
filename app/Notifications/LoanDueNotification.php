<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Interface cho Realtime
use Illuminate\Notifications\Messages\BroadcastMessage;

class LoanDueNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $loan;
    public $amount;
    public $type; // 'payment', 'create', 'due'

    /**
     * Create a new notification instance.
     */
    public function __construct($loan, $amount = 0, $type = 'payment')
    {
        $this->loan = $loan;
        $this->amount = $amount;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // 'database' để lưu vào lịch sử, 'broadcast' để đẩy realtime
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for Database.
     */
    public function toArray(object $notifiable): array
    {
        $title = 'Thông báo khoản vay';
        $message = '';

        if ($this->type === 'create') {
            $title = 'Khoản vay mới';
            $message = 'Đã tạo khoản vay mới với: ' . $this->loan->contact_name . ' - ' . number_format($this->loan->amount) . ' VNĐ';
        } elseif ($this->type === 'payment') {
            $title = 'Thanh toán khoản vay';
            $message = 'Bạn đã thanh toán ' . number_format($this->amount) . ' VNĐ cho khoản vay: ' . $this->loan->contact_name;
        }

        return [
            'title' => $title,
            'message' => $message,
            'type' => $this->type,
            'icon' => 'heroicons-outline:banknotes', // Icon hiển thị trên dropdown
            'color' => 'success', // Màu sắc (tuỳ theme hỗ trợ)
            'url' => route('finance.index'), // Link khi click vào
            'loan_id' => $this->loan->id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $title = 'Thông báo khoản vay';
        $message = '';

        if ($this->type === 'create') {
            $title = 'Khoản vay mới';
            $message = 'Đã tạo khoản vay mới với: ' . $this->loan->contact_name . ' - ' . number_format($this->loan->amount) . ' VNĐ';
        } elseif ($this->type === 'payment') {
            $title = 'Thanh toán khoản vay';
            $message = 'Bạn đã thanh toán ' . number_format($this->amount) . ' VNĐ cho khoản vay: ' . $this->loan->contact_name;
        }

        return new BroadcastMessage([
            'title' => $title,
            'message' => $message,
            'url' => route('finance.index'),
            'created_at' => now()->toIso8601String(), // Thời gian realtime
        ]);
    }
}