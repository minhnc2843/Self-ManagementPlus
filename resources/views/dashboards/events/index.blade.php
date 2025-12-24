<x-app-layout>
    <div class="space-y-8">
      
        <div class="flex justify-between items-center">
            <x-breadcrumb :page-title="'Quản Lý sự kiện'" :breadcrumb-items="[['url' => route('events.list'), 'name' => 'Lịch hẹn']]" />
        </div>  
        <style>
           
            .fc-theme-standard td, 
            .fc-theme-standard th {
                border: 1px solid black !important; 
            }
            .fc-col-header-cell-cushion {
            color: black; 
            font-weight: 800;
            padding: 8px 0;
            }
            .fc-daygrid-day-number {
                color: black;
                font-weight: 600;
                font-size: 1.1em;
                padding: 8px;
            }
            .fc-event {
                color: black;
                font-weight: 600;
            }

        </style>
        {{-- MESSAGE / ALERT --}}
        @if (session('success'))
            <div class="alert alert-success mb-6">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="heroicons:check-circle" class="text-xl"></iconify-icon>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-6">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="heroicons:x-circle" class="text-xl"></iconify-icon>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-6">
                <div class="flex gap-3">
                    <iconify-icon icon="heroicons:exclamation-triangle" class="text-xl mt-1"></iconify-icon>
                    <div>
                        <p class="font-semibold mb-1">Có lỗi xảy ra:</p>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
        

            <div class="card-body px-6 pb-6">
                {{-- Khu vực lọc (Tận dụng Dashcode style) --}}
                <div class="flex flex-wrap items-center gap-4 mb-2">
                    <form method="GET" action="{{ route('events.list') }}" class="flex flex-wrap gap-4 w-full mt-[10px]">
                        
                        {{-- Tìm kiếm (Search) --}}
                        <div class="flex-1 min-w-[200px]">
                            <label for="q" class="text-slate-500 dark:text-slate-300 block mb-1">Tìm kiếm</label>
                            <div class="relative">
                                <input type="text" name="q" id="q" class="form-control w-full pl-9" placeholder="Tiêu đề, mô tả..." value="{{ request('q') }}">
                                <iconify-icon icon="heroicons:magnifying-glass" class="absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 text-lg"></iconify-icon>
                            </div>
                        </div>

                        {{-- Lọc theo Loại (Type) --}}
                        <div class="flex-1 min-w-[150px]">
                            <label for="type" class="text-slate-500 dark:text-slate-300 block mb-1">Loại sự kiện</label>
                            <select id="type" name="type" class="form-control w-full">
                                <option value="">Tất cả</option>
                                {{-- Giả định bạn có danh sách các loại sự kiện (công việc, kỷ niệm,...) --}}
                                @foreach (['work', 'anniversary', 'holiday', 'payment', 'maintenance', 'other'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                             
                        </div>

                        {{-- Lọc theo Trạng thái (Status) --}}
                        <div class="flex-1 min-w-[150px]">
                            <label for="status" class="text-slate-500 dark:text-slate-300 block mb-1">Trạng thái</label>
                            <select id="status" name="status" class="form-control w-full">
                                <option value="">Tất cả</option>
                                @foreach (['upcoming', 'confirmed', 'attended', 'declined', 'missed', 'pending'] as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Lọc theo Quan trọng (Important) --}}
                        <div class="flex-1 min-w-[150px]">
                            <label for="important" class="text-slate-500 dark:text-slate-300 block mb-1">Mức quan trọng</label>
                            <select id="important" name="important" class="form-control w-full">
                                <option value="">Tất cả</option>
                                <option value="1" {{ request('important') == 1 ? 'selected' : '' }}>Quan trọng</option>
                            </select>
                        </div>
                        
                       <div class="flex flex-1 justify-end items-end space-x-2 whitespace-nowrap">
                        <button type="submit"
                            class="bg-gray-800 text-white px-4 h-10 rounded-md flex items-center justify-center">
                            Lọc
                        </button>

                        <a href="{{ route('events.list') }}"
                            class="bg-gray-500 text-white px-4 h-10 rounded-md flex items-center justify-center">
                            Xóa lọc
                        </a>

                        <a href="{{ route('events.create') }}"
                            class="bg-blue-600 text-white px-4 h-10 rounded-md flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m12.75 7.75h-10m5-5v10"/></svg>
                            Thêm sự kiện
                        </a>
                    </div>
                    </form>
                </div>

                {{-- Bảng danh sách sự kiện --}}
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            {{-- Thay đổi 1: Thêm border bao ngoài và tăng độ đậm đường kẻ ngang (divide-slate-200) --}}
                            <table class="min-w-full divide-y divide-slate-200 table-fixed dark:divide-slate-700 border border-slate-200 dark:border-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        {{-- Thay đổi 2: Thêm border-r (kẻ dọc), border-slate-300 (màu đậm), text-slate-800 (chữ đậm) --}}
                                        <th scope="col" class="table-th w-[20%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Title</th>
                                        <th scope="col" class="table-th w-[15%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Thời gian bắt đầu</th>
                                        <th scope="col" class="table-th w-[15%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Thời gian kết thúc</th>
                                        <th scope="col" class="table-th w-[10%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Loại</th>
                                        <th scope="col" class="table-th w-[8%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Ưu tiên</th>
                                        <th scope="col" class="table-th w-[10%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Trạng thái</th>
                                        <th scope="col" class="table-th w-[12%] border-r border-slate-300 text-slate-800 font-bold dark:text-slate-100">Quan trọng</th>
                                        <th scope="col" class="table-th w-[10%] text-slate-800 font-bold dark:text-slate-100">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-slate-200 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($events as $event)
                                        <tr @if($event->is_important) class="bg-yellow-50 dark:bg-yellow-900/20" @else class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition duration-150" @endif>
                                            
                                            {{-- Thay đổi 3: Thêm border-r border-slate-200 vào các thẻ td để chia cột --}}
                                            <td class="table-td font-medium text-slate-900 dark:text-slate-200 border-r border-slate-200 dark:border-slate-700">
                                                {{ $event->title }}
                                            </td>

                                            <td class="table-td text-sm border-r border-slate-200 dark:border-slate-700">
                                                {{ $event->start_time ? $event->start_time->format('d M, Y H:i') : 'N/A' }}
                                            </td>

                                            <td class="table-td text-sm border-r border-slate-200 dark:border-slate-700">
                                                {{ $event->end_time ? $event->end_time->format('d M, Y H:i') : 'N/A' }}
                                            </td>

                                            <td class="table-td border-r border-slate-200 dark:border-slate-700">
                                                <span class="badge bg-slate-900/60 text-slate-50 capitalize">{{ $event->event_type ?? 'Chung' }}</span>
                                            </td>
                                            
                                            <td class="table-td border-r border-slate-200 dark:border-slate-700">
                                                @php
                                                    $priorityClass = match ($event->priority) {
                                                        'high' => 'bg-red-500',
                                                        'low' => 'bg-green-500',
                                                        default => 'bg-amber-500',
                                                    };
                                                @endphp
                                                <span class="badge {{ $priorityClass }} text-white capitalize">
                                                    {{ $event->priority }}
                                                </span>
                                            </td>

                                            <td class="table-td border-r border-slate-200 dark:border-slate-700">
                                                @php
                                                    $statusClass = match ($event->status) {
                                                        'confirmed' => 'bg-success-500',
                                                        'declined' => 'bg-danger-500',
                                                        'attended' => 'bg-info-500',
                                                        'missed' => 'bg-secondary-500',
                                                        default => 'bg-primary-500',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} text-white capitalize">
                                                    {{ $event->status }}
                                                </span>
                                            </td>

                                            <td class="table-td text-center border-r border-slate-200 dark:border-slate-700">
                                                @if($event->is_important)
                                                    <span class="badge bg-danger-500 text-white capitalize"><iconify-icon icon="heroicons:star-solid" class="text-warning-500 text-lg"></iconify-icon>Important</span>
                                                @else
                                                    <span class="text-primary-500"><iconify-icon icon="heroicons:minus-circle" class="text-slate-400 text-lg"></iconify-icon>Nomal</span>
                                                @endif
                                            </td>
                                            
                                            {{-- Cột cuối cùng không cần border-r --}}
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse justify-center"> <a href="{{ route('events.edit', $event->id) }}" 
                                                        class="action-btn text-info-500" title="Chỉnh sửa">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </a>
                                                    
                                                    @if($event->status == 'upcoming' || $event->status == 'pending')
                                                        <button 
                                                            data-id="{{ $event->id }}" 
                                                            data-status="confirmed"
                                                            class="action-btn status-change-btn text-success-500"
                                                            title="Xác nhận tham gia">
                                                            <iconify-icon icon="heroicons:check-circle"></iconify-icon>
                                                        </button>
                                                        
                                                        <button 
                                                            data-id="{{ $event->id }}" 
                                                            data-status="declined"
                                                            class="action-btn status-change-btn text-danger-500"
                                                            title="Từ chối/Không tham gia">
                                                            <iconify-icon icon="heroicons:x-circle"></iconify-icon>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="8" class="table-td text-slate-500 dark:text-slate-300 py-6 border border-slate-200">Không tìm thấy sự kiện nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Phân trang --}}
                <div class="mt-6">
                    {{ $events->links() }}
                </div>

                {{-- Khu vực Lịch (Calendar) --}}
                <div class="card mt-8 border border-slate-200 dark:border-slate-700">
                    <div class="card-header px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                        <h4 class="font-medium text-lg text-slate-900 dark:text-white">Lịch tổng quan</h4>
                    </div>
                    <div class="card-body p-6">
                        <div class="overflow-x-auto">
                            <div id="calendar" class="w-full min-w-[800px] min-h-[600px]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- PHẦN SCRIPT AJAX (Quan trọng để xử lý trạng thái) --}}
    @push('scripts')
    {{-- Load FullCalendar từ CDN (hoặc bạn có thể dùng asset local của Dashcode nếu có) --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lắng nghe sự kiện click trên các nút thay đổi trạng thái
            document.querySelectorAll('.status-change-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    const eventId = this.getAttribute('data-id');
                    const newStatus = this.getAttribute('data-status');
                    // Đảm bảo token CSRF được lấy đúng cách
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}';
                    const updateUrl = '{{ url("events") }}/' + eventId + '/status';

                    if (confirm(`Bạn có chắc chắn muốn chuyển trạng thái sự kiện #${eventId} thành "${newStatus}"?`)) {
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                status: newStatus,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                // Tải lại trang để cập nhật trạng thái hiển thị
                                window.location.reload(); 
                            } else {
                                alert('Error: ' + (data.error || 'Unknown error occurred.'));
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi khi cập nhật trạng thái:', error);
                            alert('Đã xảy ra lỗi hệ thống khi cập nhật trạng thái.');
                        });
                    }
                });
            });

            // --- CẤU HÌNH FULLCALENDAR ---
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    initialView: 'dayGridMonth',
                    locale: 'vi', // Tiếng Việt
                    timeZone: 'UTC', // QUAN TRỌNG: Ngăn trình duyệt tự cộng 7 tiếng (GMT+7)
                    displayEventTime: false, // Ẩn hoàn toàn giờ trên lịch
                    navLinks: true, 
                    editable: false, // Chỉ xem, muốn sửa thì click vào sự kiện
                    dayMaxEvents: true, 
                    events: {
                        // Gọi đến hàm index() trong Controller trả về JSON
                        // Lưu ý: Đảm bảo route 'events.index' trỏ tới EventController@index
                        url: '{{ route("events.json") }}', 
                        failure: function() {
                            console.error('Không thể tải dữ liệu lịch!');
                        }
                    },
                    // Chuyển đổi dữ liệu từ DB (start_time) sang format của FullCalendar (start)
                   eventDataTransform: function(eventData) {
                    let color = '#3b82f6';
                    switch(eventData.status) {
                        case 'confirmed': color = '#22c55e'; break;
                        case 'declined': color = '#ef4444'; break;
                        case 'attended': color = '#0ea5e9'; break;
                        case 'missed': color = '#64748b'; break;
                        case 'upcoming': 
                        case 'pending': 
                        default: color = '#3b82f6'; break;
                    }

                    if (eventData.is_important) {
                        color = '#f59e0b';
                    }

                    return {
                        id: eventData.id,
                        title: eventData.title,
                        start: eventData.start_time,
                        end: eventData.end_time,
                        backgroundColor: color,
                        borderColor: color,
                        textColor: '#ffffff',
                        classNames: ['font-bold'],
                        url: '{{ url("events") }}/' + eventData.id + '/edit'
                    };
                }
                });
                calendar.render();
            }
        });
        setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.classList.add('opacity-0', 'transition', 'duration-500');
            setTimeout(() => el.remove(), 500);
        });
        }, 4000);
    </script>
    @endpush
    
</x-app-layout>