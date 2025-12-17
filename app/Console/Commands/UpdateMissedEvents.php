<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;
use App\Notifications\SystemNotification; // Dùng class notification chung

class UpdateMissedEvents extends Command
{
    protected $signature = 'events:update-missed';
    protected $description = 'Cập nhật trạng thái sự kiện đã qua và thông báo';

    public function handle()
    {
        $now = Carbon::now();
        
        // Tìm sự kiện đang 'upcoming' hoặc 'confirmed' nhưng thời gian kết thúc đã qua
        $missedEvents = Event::whereIn('status', ['upcoming', 'confirmed'])
            ->where('end_time', '<', $now)
            ->get();

        foreach ($missedEvents as $event) {
            // Cập nhật trạng thái
            $event->update(['status' => 'missed']); // Hoặc 'attended' tùy logic bạn muốn mặc định

            // Gửi thông báo cho người tạo
            if ($event->creator) {
                // Chúng ta dùng mảng data tương tự cấu trúc ở mục 2
                $event->creator->notify(new \App\Notifications\SystemNotification(
                    'Sự kiện quá hạn', // Title
                    'Sự kiện "' . $event->title . '" đã kết thúc mà chưa được cập nhật.', // Message
                    route('events.edit', $event->id), // Url
                    'danger' // Color
                ));
            }
        }

        $this->info('Đã cập nhật ' . $missedEvents->count() . ' sự kiện quá hạn.');
    }
}