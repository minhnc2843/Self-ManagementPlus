<x-app-layout>
    <div class="space-y-8">
        
        <div class="flex justify-between items-center">
            <x-breadcrumb :page-title="'Quản Lý sự kiện'" :breadcrumb-items="[['url' => route('events.list'), 'name' => 'Lịch hẹn']]" />
        </div>  
        
        {{-- Giữ nguyên Style của FullCalendar --}}
       

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

        {{-- KHU VỰC LỌC VÀ DANH SÁCH --}}
        <div class="card">
            <div class="card-body px-6 pb-6">
               <div class="mb-6">
                    <form method="GET" action="{{ route('events.list') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                        
                        <div class="lg:col-span-3">
                            <label for="q" class="text-slate-500 dark:text-slate-300 block mb-1 text-sm">Tìm kiếm</label>
                            <div class="relative">
                                <input type="text" name="q" id="q" class="form-control w-full pl-9 h-10" placeholder="Tiêu đề..." value="{{ request('q') }}">
                                <iconify-icon icon="heroicons:magnifying-glass" class="absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 text-lg"></iconify-icon>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="type" class="text-slate-500 dark:text-slate-300 block mb-1 text-sm">Loại sự kiện</label>
                            <select id="type" name="type" class="form-control w-full h-10">
                                <option value="">Tất cả</option>
                                @foreach (['work', 'anniversary', 'holiday', 'payment', 'maintenance', 'other'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="status" class="text-slate-500 dark:text-slate-300 block mb-1 text-sm">Trạng thái</label>
                            <select id="status" name="status" class="form-control w-full h-10">
                                <option value="">Tất cả</option>
                                @foreach (['upcoming', 'confirmed', 'attended', 'declined', 'missed', 'pending'] as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="important" class="text-slate-500 dark:text-slate-300 block mb-1 text-sm">Mức độ</label>
                            <select id="important" name="important" class="form-control w-full h-10">
                                <option value="">Tất cả</option>
                                <option value="1" {{ request('important') == 1 ? 'selected' : '' }}>Quan trọng</option>
                            </select>
                        </div>
                        
                        <div class="lg:col-span-3 flex gap-2 justify-end">
                            <button type="submit" class="btn btn-dark h-10 px-4 flex items-center justify-center gap-2">
                                <iconify-icon icon="heroicons:funnel"></iconify-icon>
                                <span>Lọc</span>
                            </button>
                            
                            <a href="{{ route('events.list') }}" class="btn btn-secondary h-10 px-4 flex items-center justify-center gap-2">
                                <iconify-icon icon="heroicons:arrow-path"></iconify-icon>
                                <span>Reset</span>
                            </a>
                            
                            <a href="{{ route('events.create') }}" class="btn btn-primary h-10 px-4 flex items-center justify-center gap-2">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                                <span>Thêm</span>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- DANH SÁCH SỰ KIỆN (DẠNG GRID/CARD) --}}
                <div class="grid grid-cols-1 gap-4">
                    @forelse ($events as $event)
                        @php
                            // Xử lý màu sắc Priority để làm Border trái
                            $priorityColor = match ($event->priority) {
                                'high' => 'border-danger-500',
                                'low' => 'border-success-500',
                                default => 'border-amber-500',
                            };
                            
                            // Xử lý màu Badge trạng thái
                            $statusClass = match ($event->status) {
                                'confirmed' => 'bg-success-500 text-white',
                                'declined' => 'bg-danger-500 text-white',
                                'attended' => 'bg-info-500 text-white',
                                'missed' => 'bg-secondary-500 text-white',
                                default => 'bg-primary-500 text-white',
                            };

                            // Xử lý icon quan trọng
                            $isImportant = $event->is_important;
                        @endphp

                        <div class="bg-white dark:bg-slate-800 border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border-l-[6px] {{ $priorityColor }} dark:border-slate-700 dark:border-l-[6px]">
                            <div class="p-4 sm:flex sm:items-center sm:justify-between gap-4">
                                
                                {{-- Phần 1: Thời gian (Calendar Icon style) --}}
                                <div class="flex-none flex sm:flex-col items-center justify-center bg-slate-100 dark:bg-slate-700 rounded-md p-2 min-w-[80px] text-center border border-slate-200 dark:border-slate-600 mr-3 sm:mr-0">
                                    <span class="text-xs uppercase font-bold text-slate-500 dark:text-slate-400 block">
                                        {{ $event->start_time ? $event->start_time->format('M') : '--' }}
                                    </span>
                                    <span class="text-2xl font-bold text-slate-800 dark:text-white block">
                                        {{ $event->start_time ? $event->start_time->format('d') : '--' }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 block">
                                        {{ $event->start_time ? $event->start_time->format('D') : '' }}
                                    </span>
                                </div>

                                {{-- Phần 2: Nội dung chính --}}
                                <div class="flex-1 mt-2 sm:mt-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $event->title }}
                                        </h3>
                                        @if($isImportant)
                                            <iconify-icon icon="heroicons:star-solid" class="text-warning-500 text-lg" title="Quan trọng"></iconify-icon>
                                        @endif
                                    </div>

                                    {{-- Dòng thông tin phụ --}}
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500 dark:text-slate-400">
                                        
                                        {{-- Thời gian chi tiết --}}
                                        <div class="flex items-center gap-1">
                                            <iconify-icon icon="heroicons:clock"></iconify-icon>
                                            <span>
                                                {{ $event->start_time ? $event->start_time->format('H:i') : '--' }} 
                                                - 
                                                {{ $event->end_time ? $event->end_time->format('H:i') : '--' }}
                                            </span>
                                        </div>

                                        {{-- Loại --}}
                                        <div class="flex items-center gap-1">
                                            <iconify-icon icon="heroicons:tag"></iconify-icon>
                                            <span class="capitalize">{{ $event->event_type ?? 'Chung' }}</span>
                                        </div>

                                        {{-- Ưu tiên (Hiển thị text nếu cần) --}}
                                        <div class="flex items-center gap-1">
                                            <iconify-icon icon="heroicons:flag"></iconify-icon>
                                            <span class="capitalize {{ $event->priority == 'high' ? 'text-danger-500' : '' }}">{{ $event->priority }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Phần 3: Trạng thái & Hành động --}}
                                <div class="flex-none mt-3 sm:mt-0 flex sm:flex-col items-end gap-2 justify-between sm:justify-center w-full sm:w-auto">
                                    {{-- Badge Trạng thái --}}
                                    <span class="badge {{ $statusClass }} rounded-full px-3 py-1 text-xs font-medium capitalize">
                                        {{ $event->status }}
                                    </span>

                                    {{-- Actions Buttons --}}
                                    <div class="flex items-center gap-2 mt-2">
                                        @if($event->status == 'upcoming' || $event->status == 'pending')
                                            <button data-id="{{ $event->id }}" data-status="confirmed" class="status-change-btn btn-outline-success btn-sm p-1.5 rounded-full" title="Xác nhận">
                                                <iconify-icon icon="heroicons:check" class="text-lg"></iconify-icon>
                                            </button>
                                            <button data-id="{{ $event->id }}" data-status="declined" class="status-change-btn btn-outline-danger btn-sm p-1.5 rounded-full" title="Từ chối">
                                                <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('events.edit', $event->id) }}" class="btn-outline-secondary btn-sm p-1.5 rounded-full" title="Chỉnh sửa">
                                            <iconify-icon icon="heroicons:pencil-square" class="text-lg"></iconify-icon>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-dashed border-slate-300">
                            <iconify-icon icon="heroicons:calendar-days" class="text-5xl text-slate-400 mb-2"></iconify-icon>
                            <p class="text-slate-500">Không tìm thấy sự kiện nào phù hợp.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Phân trang --}}
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            </div>
        </div>

        {{-- Khu vực Lịch (Calendar) - Giữ nguyên --}}
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
    
    {{-- PHẦN SCRIPT AJAX & FULLCALENDAR (Giữ nguyên logic của bạn) --}}
    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lắng nghe sự kiện click trên các nút thay đổi trạng thái
            document.querySelectorAll('.status-change-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    const eventId = this.getAttribute('data-id');
                    const newStatus = this.getAttribute('data-status');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}';
                    const updateUrl = '{{ url("events") }}/' + eventId + '/status';

                    if (confirm(`Bạn có chắc chắn muốn chuyển trạng thái sự kiện #${eventId} thành "${newStatus}"?`)) {
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ status: newStatus }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                window.location.reload(); 
                            } else {
                                alert('Error: ' + (data.error || 'Unknown error occurred.'));
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            alert('Đã xảy ra lỗi hệ thống.');
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
                    locale: 'vi',
                    timeZone: 'UTC', 
                    displayEventTime: false, 
                    navLinks: true, 
                    editable: false, 
                    dayMaxEvents: true, 
                    events: {
                        url: '{{ route("events.json") }}', 
                        failure: function() { console.error('Lỗi tải dữ liệu lịch!'); }
                    },
                    eventDataTransform: function(eventData) {
                        let color = '#3b82f6';
                        switch(eventData.status) {
                            case 'confirmed': color = '#22c55e'; break;
                            case 'declined': color = '#ef4444'; break;
                            case 'attended': color = '#0ea5e9'; break;
                            case 'missed': color = '#64748b'; break;
                            default: color = '#3b82f6'; break;
                        }
                        if (eventData.is_important) { color = '#f59e0b'; }
                        return {
                            id: eventData.id,
                            title: eventData.title,
                            start: eventData.start_time,
                            end: eventData.end_time,
                            backgroundColor: color,
                            borderColor: color,
                            textColor: '#ffffff',
                            url: '{{ url("events") }}/' + eventData.id + '/edit'
                        };
                    }
                });
                calendar.render();
            }
        });

        // Ẩn alert sau 4s
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                el.classList.add('opacity-0', 'transition', 'duration-500');
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>
    @endpush
</x-app-layout>