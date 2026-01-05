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
                <div class="space-y-3">
                    @forelse ($events as $event)

                    @php
                        $statusColor = match ($event->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'declined'  => 'bg-red-100 text-red-700',
                            'attended'  => 'bg-sky-100 text-sky-700',
                            'missed'    => 'bg-slate-200 text-slate-700',
                            default     => 'bg-indigo-100 text-indigo-700',
                        };

                        $priorityDot = match ($event->priority) {
                            'high' => 'bg-red-500',
                            'low'  => 'bg-emerald-500',
                            default => 'bg-amber-400',
                        };
                    @endphp

                    <div
                        class="group flex flex-col sm:flex-row sm:items-center gap-4
                            rounded-xl border border-slate-200 dark:border-slate-700
                            bg-white dark:bg-slate-800
                            px-4 py-3
                            hover:shadow-md transition">

                        {{-- 🗓 Date --}}
                        <div class="min-w-[160px]">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">
                                {{ $event->start_time?->translatedFormat('l') ?? '—' }}
                            </p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">
                                {{ $event->start_time?->format('d/m/Y') ?? '--/--/----' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $event->start_time?->format('H:i') ?? '--:--' }}
                                –
                                {{ $event->end_time?->format('H:i') ?? '--:--' }}
                            </p>
                        </div>

                        {{-- 📄 Content --}}
                        <div class="flex-1 space-y-1">

                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $priorityDot }}"></span>

                                <h3 class="font-medium text-slate-900 dark:text-white">
                                    {{ $event->title }}
                                </h3>

                                @if($event->is_important)
                                    <iconify-icon
                                        icon="heroicons:star-solid"
                                        class="text-amber-400 text-sm"
                                        title="Quan trọng"/>
                                @endif
                            </div>

                            <div class="text-sm text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-4">
                                <span>{{ $event->event_type ?? 'Chung' }}</span>
                                <span class="capitalize">Priority: {{ $event->priority }}</span>
                            </div>
                        </div>

                        {{-- 🎯 Status + Actions --}}
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">

                                                {{-- Status --}}
                                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>

                                                {{-- Actions (ALWAYS VISIBLE) --}}
                                                <div class="flex items-center gap-1">

                                                    @if(in_array($event->status, ['upcoming','pending']))
                                                        <button
                                                            data-id="{{ $event->id }}"
                                                            data-status="confirmed"
                                                            class="status-change-btn
                                                                w-9 h-9 rounded-full
                                                                flex items-center justify-center
                                                                bg-emerald-50 text-emerald-600
                                                                hover:bg-emerald-100"
                                                            title="Xác nhận">
                                                            <iconify-icon icon="heroicons:check-circle"/>
                                                        </button>

                                                        <button
                                                            data-id="{{ $event->id }}"
                                                            data-status="declined"
                                                            class="status-change-btn
                                                                w-9 h-9 rounded-full
                                                                flex items-center justify-center
                                                                bg-red-50 text-red-600
                                                                hover:bg-red-100"
                                                            title="Từ chối">
                                                            <iconify-icon icon="heroicons:x-circle"/>
                                                        </button>
                                                    @endif

                                                    <a
                                                        href="{{ route('events.show', $event->id) }}"
                                                        class="w-9 h-9 rounded-full
                                                            flex items-center justify-center
                                                            bg-sky-50 text-sky-600
                                                            hover:bg-sky-100"
                                                        title="Xem chi tiết">
                                                        <iconify-icon icon="heroicons:eye"/>
                                                    </a>

                                                    <a
                                                        href="{{ route('events.edit', $event->id) }}"
                                                        class="w-9 h-9 rounded-full
                                                            flex items-center justify-center
                                                            bg-slate-100 text-slate-600
                                                            hover:bg-slate-200"
                                                        title="Chỉnh sửa">
                                                        <iconify-icon icon="heroicons:pencil-square"/>
                                                    </a>
                                                </div>
                                            </div>

                    </div>

                    @empty
                    <div class="text-center py-12 text-slate-500">
                        <iconify-icon icon="heroicons:calendar-days" class="text-5xl mb-2 opacity-50"/>
                        <p>Không có sự kiện nào</p>
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
                            url: '{{ url("events/show") }}/' + eventData.id
                            // url: '{{ url("events") }}/' + eventData.id + '/edit'
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