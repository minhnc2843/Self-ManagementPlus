<x-app-layout>
    <div class="space-y-8">
        <div>
            <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>
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
                    <form method="GET" action="{{ route('events.list') }}" class="flex flex-wrap gap-4">
                        
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
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th w-[20%]">Title</th>
                                        <th scope="col" class="table-th w-[15%]">Thời gian bắt đầu</th>
                                        <th scope="col" class="table-th w-[15%]">Thời gian kết thúc</th>
                                        <th scope="col" class="table-th w-[10%]">Loại</th>
                                        <th scope="col" class="table-th w-[8%]">Ưu tiên</th>
                                        <th scope="col" class="table-th w-[10%]">Trạng thái</th>
                                        <th scope="col" class="table-th w-[12%]">Quan trọng</th>
                                        <th scope="col" class="table-th w-[10%]">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($events as $event)
                                        <tr @if($event->is_important) class="bg-yellow-50 dark:bg-yellow-900/20" @endif>
                                            <td class="table-td font-medium text-slate-900 dark:text-slate-200">
                                                {{ $event->title }}
                                            </td>

                                            <td class="table-td text-sm">
                                                {{ $event->start_time ? $event->start_time->format('d M, Y H:i') : 'N/A' }}
                                            </td>

                                            <td class="table-td text-sm">
                                                {{ $event->end_time ? $event->end_time->format('d M, Y H:i') : 'N/A' }}
                                            </td>

                                            <td class="table-td">
                                                <span class="badge bg-slate-900/60 text-slate-50 capitalize">{{ $event->event_type ?? 'Chung' }}</span>
                                            </td>
                                            
                                            {{-- Cột Priority --}}
                                            <td class="table-td">
                                                @php
                                                    $priorityClass = match ($event->priority) {
                                                        'high' => 'bg-red-500',
                                                        'low' => 'bg-green-500',
                                                        default => 'bg-amber-500', // normal
                                                    };
                                                @endphp
                                                <span class="badge {{ $priorityClass }} text-white capitalize">
                                                    {{ $event->priority }}
                                                </span>
                                            </td>

                                            {{-- Cột Status --}}
                                            <td class="table-td">
                                                @php
                                                    $statusClass = match ($event->status) {
                                                        'confirmed' => 'bg-success-500',
                                                        'declined' => 'bg-danger-500',
                                                        'attended' => 'bg-info-500',
                                                        'missed' => 'bg-secondary-500',
                                                        default => 'bg-primary-500', // upcoming/pending
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} text-white capitalize">
                                                    {{ $event->status }}
                                                </span>
                                            </td>

                                            {{-- Cột Important (Đã sửa icon) --}}
                                            <td class="table-td text-center">
                                                @if($event->is_important)
                                                    <span class="badge bg-danger-500 text-white capitalize"><iconify-icon icon="heroicons:star-solid" class="text-warning-500 text-lg"></iconify-icon>Important</span>
                                                @else
                                                     <span class="text-primary-500"><iconify-icon icon="heroicons:minus-circle" class="text-slate-400 text-lg"></iconify-icon>Nomal</span>
                                                @endif
                                            </td>
                                            
                                            {{-- Cột Hành động (Đã thêm link Sửa) --}}
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    
                                                    {{-- Link Sửa (Edit) --}}
                                                    <a href="{{ route('events.edit', $event->id) }}" 
                                                        class="action-btn text-info-500" title="Chỉnh sửa">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </a>
                                                    
                                                    {{-- Nút Xác nhận (Confirmed) --}}
                                                    @if($event->status == 'upcoming' || $event->status == 'pending')
                                                        <button 
                                                            data-id="{{ $event->id }}" 
                                                            data-status="confirmed"
                                                            class="action-btn status-change-btn text-success-500"
                                                            title="Xác nhận tham gia">
                                                            <iconify-icon icon="heroicons:check-circle"></iconify-icon>
                                                        </button>
                                                        
                                                        {{-- Nút Từ chối (Declined) --}}
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
                                            <td colspan="8" class="table-td text-slate-500 dark:text-slate-300 py-6">Không tìm thấy sự kiện nào.</td>
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
            </div>
        </div>
    </div>
    
    {{-- PHẦN SCRIPT AJAX (Quan trọng để xử lý trạng thái) --}}
    @push('scripts')
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