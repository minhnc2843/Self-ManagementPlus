<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventReminder;
use App\Models\EventHistory;
use App\Services\EventRepeatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\SystemNotification;
class EventController extends Controller
{
    protected $repeatService;

    // Các giá trị hợp lệ cho Enum
    const VALID_STATUSES = ['upcoming', 'confirmed', 'attended', 'declined', 'missed', 'pending'];
    const VALID_PRIORITIES = ['low', 'normal', 'high'];
    const VALID_REPEAT_RULES = ['null', 'daily', 'weekly', 'monthly', 'yearly', 'custom'];
    const EVENT_TYPES = ['Công việc', 'Kỷ niệm', 'Ngày lễ', 'Thanh toán', 'Bảo dưỡng', 'Khác'];

    // Giả định EventRepeatService được inject
    public function __construct(EventRepeatService $repeatService)
    {
        $this->repeatService = $repeatService;
    }

    // --- VIEW FUNCTIONS ---

    /**
     * Hiển thị danh sách sự kiện ra view (events.list)
     */
    public function showList(Request $request)
    {
        $pageTitle = 'Danh Sách Lịch Hẹn';
        $breadcrumbItems = [
            ['name' => 'Lịch Hẹn', 'url' => route('events.list'), 'active' => true],
        ];

        $query = Event::query();

        // Áp dụng bộ lọc từ request (GET)
        if ($request->filled('type')) {
            $query->where('event_type', $request->type);
        }
        if ($request->filled('status') && in_array($request->status, self::VALID_STATUSES)) {
            $query->where('status', $request->status);
        }
        if ($request->boolean('important')) {
            $query->where('is_important', true);
        }
        // Logic tìm kiếm
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Giảm số lượng bản ghi mỗi trang xuống (ví dụ 10) để dễ dàng kiểm tra hiển thị phân trang khi ít dữ liệu
        $events = $query->orderBy('start_time', 'desc')->paginate(10)->withQueryString();

        return view('dashboards.events.index', compact('pageTitle', 'breadcrumbItems', 'events'));
    }
    
    /**
     * Hiển thị form tạo mới sự kiện (events.create)
     */
    public function create()
    {
        $pageTitle = 'Tạo Sự Kiện Mới';
        $breadcrumbItems = [
            ['name' => 'Lịch Hẹn', 'url' => route('events.list'), 'active' => false],
            ['name' => 'Tạo mới', 'url' => '#', 'active' => true],
        ];

        $eventTypes = self::EVENT_TYPES;
        $priorities = self::VALID_PRIORITIES;

        return view('dashboards.events.create', compact('pageTitle', 'breadcrumbItems', 'eventTypes', 'priorities'));
    }

    /**
     * Hiển thị form chỉnh sửa sự kiện (events.edit)
     */
    public function edit($id)
    {
        $event = Event::with('reminders')->findOrFail($id);
        
        $pageTitle = 'Chỉnh Sửa Sự Kiện: ' . $event->title;
        $breadcrumbItems = [
            ['name' => 'Lịch Hẹn', 'url' => route('events.list'), 'active' => false],
            ['name' => 'Chỉnh sửa', 'url' => '#', 'active' => true],
        ];

        $eventTypes = self::EVENT_TYPES;
        $priorities = self::VALID_PRIORITIES;

        return view('dashboards.events.edit', compact('pageTitle', 'breadcrumbItems', 'event', 'eventTypes', 'priorities'));
    }

    // --- API FUNCTIONS (DATA HANDLING) ---

    /**
     * Lấy danh sách lịch hẹn (cho API/FullCalendar/AJAX)
     */
    public function index(Request $request)
    {
        $query = Event::with('reminders');
        if ($request->has('start') && $request->has('end')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start, $request->end])
                  ->orWhereBetween('end_time', [$request->start, $request->end]);
            });
        }
        if ($request->has('status') && in_array($request->status, self::VALID_STATUSES)) {
            $query->where('status', $request->status);
        }
        $events = $query->orderBy('start_time', 'asc')->get();
        return response()->json($events);
    }



   public function store(Request $request)
{
    // 1. Gộp ngày + giờ
    $startDateTime = $this->combineDateTime(
        $request->input('start_date'),
        $request->input('start_time_hour')
    );

    if (!$startDateTime) {
        return back()->withErrors(['start_time' => 'Start time is required']);
    }

    $endDateTime = null;
    if ($request->filled('end_date')) {
        $endDateTime = $this->combineDateTime(
            $request->input('end_date'),
            $request->input('end_time_hour')
        );
    }

    $request->merge([
        'start_time' => $startDateTime,
        'end_time'   => $endDateTime,
    ]);

    // 2. Validation (KHÔNG ép repeat)
    $validated = $request->validate([
        'title'        => 'required|string|max:255',
        'description'  => 'nullable|string',
        'event_type'   => 'nullable|string|max:100',
        'location'     => 'nullable|string|max:255',

        'start_time'   => 'required|date_format:Y-m-d\TH:i:s',
        'end_time'     => 'nullable|date_format:Y-m-d\TH:i:s|after_or_equal:start_time',

        'repeat_rule'  => 'nullable|in:daily,weekly,monthly,yearly,custom',
        'repeat_meta'  => 'nullable|array',

        'priority'     => 'nullable|in:low,normal,high',
        'is_important' => 'nullable|boolean',
        'reminders'    => 'nullable|array',
    ]);

    // 3. Chuẩn hóa repeat_rule (RẤT QUAN TRỌNG)
    $repeatRule = $request->filled('repeat_rule')
        ? $request->input('repeat_rule')
        : null;

    // 4. Ép repeat_meta CHỈ khi thật sự có lặp
    if (in_array($repeatRule, ['weekly', 'custom'], true)) {
        if (empty($validated['repeat_meta'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'repeat_meta' => 'Repeat meta is required for this rule.'
                ]);
        }
    }

    // 5. Chuẩn hóa datetime cho DB
    $startCarbon = Carbon::parse($validated['start_time']);
    $validated['start_time'] = $startCarbon->format('Y-m-d H:i:s');

    if (!empty($validated['end_time'])) {
        $validated['end_time'] = Carbon::parse($validated['end_time'])
            ->format('Y-m-d H:i:s');
    } else {
        $validated['end_time'] = $startCarbon
            ->copy()
            ->addDay()
            ->startOfDay()
            ->format('Y-m-d H:i:s');
    }

    // 6. Reminders
    $reminders = $validated['reminders'] ?? [];
    unset($validated['reminders']);

    if (empty($reminders)) {
        $defaultReminder = $startCarbon->copy()->subDay();
        if ($defaultReminder->isFuture()) {
            $reminders[] = $defaultReminder->format('Y-m-d H:i:s');
        }
    }

    DB::beginTransaction();
    try {
        // 7. Create Event
        $event = Event::create([
            ...$validated,
            'created_by'   => Auth::id(),
            'status'       => 'upcoming',
            'is_important' => (bool) $request->input('is_important', false),
            'repeat_rule'  => $repeatRule, // NULL nếu không chọn
        ]);

        // 8. Insert Reminders
        $rows = [];
        foreach ($reminders as $timeString) {
            try {
                $time = $this->parseReminderTime($timeString);
                if ($time && $time->isFuture()) {
                    $rows[] = [
                        'event_id'   => $event->id,
                        'remind_at'  => $time->format('Y-m-d H:i:s'),
                        'is_sent'    => false,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ];
                }
            } catch (\Throwable $e) {}
        }

        if ($rows) {
            EventReminder::insert($rows);
        }

        // 9. History
        EventHistory::create([
            'event_id' => $event->id,
            'user_id'  => Auth::id(),
            'action'   => 'created',
            'old_data' => null,
            'new_data' => $event->toArray(),
        ]);

        // 10. Repeat
        if ($repeatRule) {
            $this->repeatService->createNextRepeatedEvent($event);
        }

        DB::commit();
        $user = Auth::user();
        $user->notify(new SystemNotification(
            'Sự kiện mới',                          // $action (Loại)
            $event->title,                          // $title (Tên sự kiện)
            'Bạn đã tạo lịch hẹn'   , // $message
            route('events.edit', $event->id),       // $url
            'success'                               // $type
        ));
        return redirect()
            ->route('events.list')
            ->with('success', 'Sự kiện đã được tạo thành công.');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => 'Create event failed: ' . $e->getMessage()
            ]);
    }
}



    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    // 1. Gộp datetime
    $startDateTime = $this->combineDateTime(
        $request->input('start_date'),
        $request->input('start_time_hour')
    );

    if (!$startDateTime) {
        return back()->withErrors(['start_time' => 'Start time is required']);
    }

    $request->merge(['start_time' => $startDateTime]);

    $shouldRemoveEndTime = $request->boolean('remove_end_time');

    if ($shouldRemoveEndTime || !$request->filled('end_date')) {
        $request->merge(['end_time' => null]);
    } else {
        $request->merge([
            'end_time' => $this->combineDateTime(
                $request->input('end_date'),
                $request->input('end_time_hour')
            )
        ]);
    }

    // 2. Validation (KHÔNG so sánh thời gian ở đây)
    $validated = $request->validate([
        'title'        => 'required|string|max:255',
        'description'  => 'nullable|string',
        'event_type'   => 'nullable|string|max:100',
        'location'     => 'nullable|string|max:255',

        'start_time'   => 'required|date_format:Y-m-d\TH:i:s',
        'end_time'     => 'nullable|date_format:Y-m-d\TH:i:s',

        'repeat_rule'  => 'nullable|in:daily,weekly,monthly,yearly,custom',
        'repeat_meta'  => 'nullable',
        'priority'     => 'nullable|in:low,normal,high',
        'is_important' => 'nullable|boolean',
        'reminders'    => 'nullable|array',
    ]);

    // 3. repeat_rule
    $repeatRule = $request->filled('repeat_rule')
        ? $request->repeat_rule
        : null;

    if (in_array($repeatRule, ['weekly', 'custom'], true) && empty($validated['repeat_meta'])) {
        return back()
            ->withInput()
            ->withErrors(['repeat_meta' => 'Repeat meta is required']);
    }

    // 4. Chuẩn hóa datetime DB + so sánh
    $startCarbon = Carbon::parse($validated['start_time']);

    if (!empty($validated['end_time'])) {
        $endCarbon = Carbon::parse($validated['end_time']);

        if ($endCarbon->lt($startCarbon)) {
            return back()
                ->withInput()
                ->withErrors([
                    'end_time' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.'
                ]);
        }

        $validated['end_time'] = $endCarbon->format('Y-m-d H:i:s');
    } elseif (!$shouldRemoveEndTime) {
        $validated['end_time'] = $startCarbon
            ->copy()
            ->addDay()
            ->startOfDay()
            ->format('Y-m-d H:i:s');
    } else {
        $validated['end_time'] = null;
    }

    $validated['start_time'] = $startCarbon->format('Y-m-d H:i:s');

    // 5. Reminders
    $reminders = $validated['reminders'] ?? [];
    unset($validated['reminders']);

    DB::beginTransaction();
    try {
        $oldEvent = $event->toArray();

        // 6. Update event
        $event->update([
            ...$validated,
            'repeat_rule'  => $repeatRule,
            'is_important' => (bool) $request->input('is_important', false),
        ]);

        // 7. Update reminders
        EventReminder::where('event_id', $event->id)->delete();

        $rows = [];
        if (empty($reminders)) {
            $defaultReminder = $startCarbon->copy()->subDay();
            if ($defaultReminder->isFuture()) {
                $reminders[] = $defaultReminder->format('Y-m-d H:i:s');
            }
        }

        foreach ($reminders as $timeString) {
            try {
                $time = $this->parseReminderTime($timeString);
                if ($time && $time->isFuture()) {
                    $rows[] = [
                        'event_id'   => $event->id,
                        'remind_at'  => $time->format('Y-m-d H:i:s'),
                        'is_sent'    => false,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ];
                }
            } catch (\Throwable $e) {}
        }

        if ($rows) {
            EventReminder::insert($rows);
        }

        // 8. History
        EventHistory::create([
            'event_id' => $event->id,
            'user_id'  => Auth::id(),
            'action'   => 'updated',
            'old_data' => $oldEvent,
            'new_data' => $event->fresh()->toArray(),
        ]);

        // 9. Repeat
        if ($repeatRule && $repeatRule !== $oldEvent['repeat_rule']) {
            $this->repeatService->createNextRepeatedEvent($event);
        }

        DB::commit();
        $user = Auth::user();
        $user->notify(new SystemNotification(
            'Cập nhật sự kiện',                     // $action
            $event->title,                          // $title
            'Thông tin lịch hẹn đã được thay đổi.', // $message
            route('events.edit', $event->id),       // $url
            'warning'                               // $type
        ));
        return redirect()
            ->route('events.list')
            ->with('success', 'Sự kiện đã được cập nhật thành công.');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors(['error' => 'Cập nhật thất bại: ' . $e->getMessage()]);
    }
}


    /**
     * Cập nhật trạng thái (events.status)
     * Thường dùng AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|in:' . implode(',', self::VALID_STATUSES),
            'is_important' => 'nullable|boolean',
        ]);
        
        if (!$request->has('status') && !$request->has('is_important')) {
             return response()->json(['error' => 'Status or importance is required.'], 422);
        }

        $oldStatus = $event->status;
        $oldImportant = $event->is_important;
        $oldData = ['status' => $oldStatus, 'is_important' => $oldImportant];

        DB::beginTransaction();
        try {
            if ($request->has('status')) {
                $event->status = $validated['status'];
            }
            if ($request->has('is_important')) {
                $event->is_important = (bool)$validated['is_important'];
            }
            
            $event->save();

            $newData = ['status' => $event->status, 'is_important' => $event->is_important];

            if ($oldStatus !== $event->status || $oldImportant !== $event->is_important) {
                 EventHistory::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'action' => 'status_or_important_changed',
                    'old_data' => $oldData,
                    'new_data' => $newData,
                ]);
            }
           
            DB::commit();
           $user = Auth::user();
            $user->notify(new SystemNotification(
                'Xác nhận lịch hẹn thành công',                     // $action
                $event->title,                          // $title
                'Thông tin lịch hẹn đã được confirm.', // $message
                route('events.edit', $event->id),       // $url
                'warning'                               // $type
            ));
            return response()->json(['message' => 'Status updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }
    
    // --- HÀM HỖ TRỢ XỬ LÝ THỜI GIAN ---

    /**
     * Ghép input date và time hour từ form thành chuỗi YYYY-MM-DDTHH:MM:SS
     * Nếu time bị bỏ trống, mặc định là 00:00:00.
     */
   // Trong EventController.php

protected function combineDateTime($date, $time_hour = '00:00')
{
    // Yêu cầu format YYYY-MM-DDTHH:MM:00 để khớp với validation date_format:Y-m-d\TH:i:s
    
    if (empty($date)) {
        return null;
    }
    
    // Đảm bảo $time_hour có giá trị, mặc định là '00:00' nếu trống
    $time = $time_hour ?? '00:00'; 
    
    // Chuỗi kết quả: 2025-12-08T12:00:00
    return "{$date}T{$time}:00"; 
}
    
    /**
     * Phân tích cú pháp thời gian nhắc nhở (vì nó có thể là datetime-local hoặc chỉ là ngày)
     */
    protected function parseReminderTime($timeString)
    {
        // Chấp nhận chuỗi YYYY-MM-DDTHH:MM
        $timeString = str_replace('T', ' ', $timeString);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeString)) {
            // Nếu chỉ là ngày, thêm giờ mặc định
            return Carbon::parse($timeString . ' 00:00:00');
        }
        
        // Cố gắng phân tích cú pháp
        return Carbon::parse($timeString);
    }
}