<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventReminder;
use App\Models\EventHistory;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;

class AutoSendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Gửi thông báo nhắc lịch theo remind_at';

    public function handle()
    {
        $now = Carbon::now();

        $this->info("Bắt đầu gửi các reminders quá hạn...");

        // Lấy và xử lý reminders theo từng nhóm nhỏ để tối ưu bộ nhớ
        EventReminder::where('is_sent', false)
            ->where('remind_at', '<=', $now)
            ->with('event.creator')
            ->chunkById(100, function ($reminders) {
                foreach ($reminders as $r) {
                    // Nếu event hoặc người tạo event không tồn tại -> bỏ qua
                    if (!$r->event || !$r->event->creator) {
                        $r->update(['is_sent' => true]);
                        continue;
                    }

                    // Gửi notification (database + realtime)
                    $r->event->creator->notify(new EventReminderNotification($r->event));

                    // Ghi vào lịch sử
                    EventHistory::create([
                        'event_id' => $r->event_id,
                        'user_id'  => $r->event->created_by,
                        'action'   => 'send_reminder',
                        'old_data' => null,
                        'new_data' => ['remind_at' => $r->remind_at],
                    ]);

                    // Đánh dấu đã gửi để tránh gửi lại
                    $r->update(['is_sent' => true]);
                }
                $this->info("Đã xử lý " . count($reminders) . " reminders.");
            });

        $this->info("Hoàn tất việc gửi reminders.");
    }
}
