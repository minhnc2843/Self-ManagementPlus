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

        $events = $query->orderBy('start_time', 'desc')->paginate(15)->withQueryString();

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

    /**
     * Tạo lịch hẹn mới (events.store)
     */
    // Trong EventController.php

    // Trong EventController.php

    public function store(Request $request)
    {
        // --- 1. CHUẨN BỊ DỮ LIỆU GHÉP TỪ DATE VÀ TIME INPUT ---
        
        // Start Time: date là bắt buộc, time là tùy chọn (mặc định 00:00:00)
        $startDateTime = $this->combineDateTime($request->input('start_date'), $request->input('start_time_hour'));
        
        // End Time: Cả date và time đều tùy chọn
        $endDateTime = $this->combineDateTime($request->input('end_date'), $request->input('end_time_hour'));
        
        // Ghi đè vào request để Validation có thể kiểm tra định dạng
        $request->merge(['start_time' => $startDateTime]);
        if ($endDateTime) {
            $request->merge(['end_time' => $endDateTime]);
        }

        // --- 2. VALIDATION ---
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            
            'start_time' => 'required|date_format:Y-m-d\TH:i:s|after_or_equal:now', 
            'end_time' => 'nullable|date_format:Y-m-d\TH:i:s|after_or_equal:start_time',
            
            'repeat_rule' => 'nullable|in:' . implode(',', self::VALID_REPEAT_RULES),
            'repeat_meta' => 'nullable|array',
            'priority' => 'nullable|in:' . implode(',', self::VALID_PRIORITIES),
            'is_important' => 'nullable|boolean',
            'reminders' => 'nullable|array',
        ]);
        
        // Xử lý Repeat Rule và Meta
        if (($request->repeat_rule === 'custom' || $request->repeat_rule === 'weekly') && empty($request->repeat_meta)) {
            return back()->withInput()->withErrors(['repeat_meta' => 'Repeat meta is required for custom/weekly rule.']);
        }
        
        // Chuyển đổi định dạng thời gian cho DB
        $startCarbon = Carbon::parse($validatedData['start_time']);
        $validatedData['start_time'] = $startCarbon->format('Y-m-d H:i:s');
        
        // --- 3. LOGIC MỚI: XỬ LÝ END_TIME MẶC ĐỊNH (00:00:00 ngày kế tiếp) ---
        if (empty($validatedData['end_time'])) {
            // Nếu end_time trống, đặt là 00:00:00 của ngày kế tiếp sau start_time
            $validatedData['end_time'] = $startCarbon->copy()->addDay()->startOfDay()->format('Y-m-d H:i:s');
        } else {
            // Nếu có end_time, chuyển đổi sang format DB
            $validatedData['end_time'] = Carbon::parse($validatedData['end_time'])->format('Y-m-d H:i:s');
        }
        
        // Xử lý Reminders mặc định... (phần này không đổi)
        $remindersData = $validatedData['reminders'] ?? [];
        if (empty($remindersData)) {
            $remindersData[] = $startCarbon->copy()->subDay()->format('Y-m-d H:i:s');
        }

        // ... (Phần logic còn lại của store() không thay đổi: Transaction, Lưu, History, Redirect) ...
        DB::beginTransaction();
        try {
            $eventData = array_merge($validatedData, [
                'created_by' => Auth::id(),
                'status' => 'upcoming',
                'is_important' => $request->has('is_important') ? (bool)$request->input('is_important') : false,
                'repeat_rule' => $validatedData['repeat_rule'] ?? 'null',
            ]);
            
            unset($eventData['reminders']);
            $event = Event::create($eventData);

            // Lưu Reminders
            $remindersToInsert = [];
            foreach ($remindersData as $timeString) {
                try {
                    $time = $this->parseReminderTime($timeString);
                    if ($time && $time->isFuture()) {
                        $remindersToInsert[] = [
                            'event_id' => $event->id,
                            'remind_at' => $time->format('Y-m-d H:i:s'),
                            'is_sent' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } catch (\Exception $e) {}
            }
            if (!empty($remindersToInsert)) {
                EventReminder::insert($remindersToInsert);
            }

            // Lưu lịch sử và Xử lý lặp lại...
            EventHistory::create(['event_id' => $event->id, 'user_id' => Auth::id(), 'action' => 'created', 'old_data' => null, 'new_data' => $event->toArray(),]);

            if ($event->repeat_rule !== 'null') {
                $this->repeatService->createNextRepeatedEvent($event);
            }

            DB::commit();
            return redirect()->route('events.list')->with('success', 'Sự kiện đã được tạo thành công.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()]);
        }
    }


    /**
     * Cập nhật lịch hẹn (events.update)
     */
    // Trong EventController.php

       // Trong EventController.php

   // Trong EventController.php

    // Trong EventController.php

    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);
    
    // --- 1. CHUẨN BỊ VÀ GỘP DỮ LIỆU THỜI GIAN ---
    $startDateTime = $this->combineDateTime($request->input('start_date'), $request->input('start_time_hour'));
    $request->merge(['start_time' => $startDateTime]);

    $shouldRemoveEndTime = $request->boolean('remove_end_time'); // Đọc trước validation

    if ($shouldRemoveEndTime || empty($request->input('end_date'))) {
        $request->merge(['end_time' => null]);
        $endDateTime = null;
    } else {
        $endDateTime = $this->combineDateTime($request->input('end_date'), $request->input('end_time_hour'));
        $request->merge(['end_time' => $endDateTime]);
    }

    // Merge remove_end_time = null để tránh prohibited (nếu bạn muốn giữ rule, comment dòng này và xóa prohibited)
    $request->merge(['remove_end_time' => null]);

    // --- 2. VALIDATION (Strict hơn, copy từ store + fix) ---
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'event_type' => 'nullable|in:' . implode(',', array_map(fn($t) => "'$t'", self::EVENT_TYPES)), // Thêm in: nếu cần strict
        'location' => 'nullable|string|max:255',
        
        'start_time' => 'required|date_format:Y-m-d\TH:i:s|after_or_equal:now', // Thêm after_or_equal:now như store
        'end_time' => 'nullable|date_format:Y-m-d\TH:i:s|after_or_equal:start_time',
        
        'repeat_rule' => 'nullable|in:' . implode(',', self::VALID_REPEAT_RULES), // Fix: thêm in:
        'repeat_meta' => 'nullable|array',
        'priority' => 'nullable|in:' . implode(',', self::VALID_PRIORITIES), // Fix: thêm in:
        'is_important' => 'nullable|boolean',
        'reminders' => 'nullable|array',
        // 'remove_end_time' => 'boolean', // Nếu muốn validate, dùng boolean thay prohibited
    ]);

    // Validate repeat_meta nếu custom/weekly (như store)
    if (($validatedData['repeat_rule'] ?? null) === 'custom' || ($validatedData['repeat_rule'] ?? null) === 'weekly') {
        if (empty($validatedData['repeat_meta'])) {
            return back()->withInput()->withErrors(['repeat_meta' => 'Repeat meta is required for custom/weekly rule.']);
        }
    }

    // --- 3. XỬ LÝ DỮ LIỆU THỜI GIAN ---
    $startCarbon = Carbon::parse($validatedData['start_time']);
    $validatedData['start_time'] = $startCarbon->format('Y-m-d H:i:s');
    
    if (empty($validatedData['end_time']) && !$shouldRemoveEndTime) {
        $validatedData['end_time'] = $startCarbon->copy()->addDay()->startOfDay()->format('Y-m-d H:i:s');
    } elseif (isset($validatedData['end_time']) && $validatedData['end_time']) {
        $validatedData['end_time'] = Carbon::parse($validatedData['end_time'])->format('Y-m-d H:i:s');
    } // Else: null nếu remove

    $oldEvent = $event->toArray(); // Lưu old cho history
    $remindersInput = $validatedData['reminders'] ?? []; // Copy trước unset
    unset($validatedData['reminders']); // Unset để update event

    // --- 4. TRANSACTION ---
    DB::beginTransaction();
    try {
        // Update Event
        $event->update($validatedData);

        // Xử lý Reminders (di chuyển vào đây, fix lặp)
        EventReminder::where('event_id', $event->id)->delete();
        $remindersToInsert = [];

        $remindersData = $remindersInput; // Dùng copy
        if (empty($remindersData)) {
            $defaultReminder = $startCarbon->copy()->subDay();
            if ($defaultReminder->isFuture()) {
                $remindersData[] = $defaultReminder->format('Y-m-d H:i:s');
            }
        }

        foreach ($remindersData as $timeString) {
            try {
                // Giả sử input là Y-m-dTH:i, thêm :00 nếu cần
                if (strpos($timeString, ':') === false || substr_count($timeString, ':') === 1) {
                    $timeString .= ':00';
                }
                $timeString = str_replace('T', ' ', $timeString);
                $time = Carbon::parse($timeString);
                
                if ($time->isFuture()) {
                    $remindersToInsert[] = [
                        'event_id' => $event->id,
                        'remind_at' => $time->format('Y-m-d H:i:s'),
                        'is_sent' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } catch (\Exception $e) {
                // Log nếu cần: \Log::error("Reminder parse error: " . $e->getMessage());
            }
        }
        if (!empty($remindersToInsert)) {
            EventReminder::insert($remindersToInsert);
        }

        // Lưu History
        EventHistory::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'old_data' => $oldEvent,
            'new_data' => $event->fresh()->toArray(),
        ]);

        // Xử lý Repeat nếu thay đổi rule (basic, mở rộng nếu cần)
        $oldRule = $oldEvent['repeat_rule'] ?? 'null';
        $newRule = $validatedData['repeat_rule'] ?? 'null';
        if ($newRule !== $oldRule && $newRule !== 'null') {
            // Xóa old repeats nếu cần (giả sử service có method)
            // $this->repeatService->deleteRelatedRepeats($event);
            $this->repeatService->createNextRepeatedEvent($event);
        } elseif ($newRule === 'null' && $oldRule !== 'null') {
            // Xóa related repeats
            // $this->repeatService->deleteRelatedRepeats($event);
        }

        DB::commit();
        return redirect()->route('events.list')->with('success', 'Sự kiện đã được cập nhật thành công.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => 'Cập nhật thất bại: ' . $e->getMessage()]);
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