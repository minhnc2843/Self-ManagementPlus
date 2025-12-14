<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventHistory;
use Carbon\Carbon;
use App\Services\EventRepeatService;

class AutoUpdateMissedEvents extends Command
{
    protected $signature = 'events:update-missed';

    protected $description = 'Tự động đánh dấu sự kiện bỏ lỡ (missed) và sinh lịch lặp tiếp theo nếu có';

    public function handle()
    {
        $now = Carbon::now();

        $this->info("Bắt đầu cập nhật missed events...");

        // Lấy và xử lý sự kiện theo từng nhóm nhỏ
        Event::where('status', 'upcoming')
            ->where('start_time', '<', $now)
            ->chunkById(100, function ($events) {
                $service = app(EventRepeatService::class);

                foreach ($events as $event) {
                    // Đánh dấu missed
                    $oldStatus = $event->status;
                    $event->update(['status' => 'missed']);

                    EventHistory::create([
                        'event_id' => $event->id,
                        'user_id'  => $event->created_by,
                        'action'   => 'missed',
                        'old_data' => ['status' => $oldStatus],
                        'new_data' => ['status' => 'missed'],
                    ]);

                    // Nếu có luật lặp lại, tạo sự kiện tiếp theo
                    $service->createNextRepeatedEvent($event);
                }
                $this->info("Đã xử lý "- count($events) . " events.");
            });

        $this->info("Hoàn tất cập nhật missed events.");
    }
}
