<x-app-layout>
<div class="space-y-5 profile-page">
    <div class="flex justify-between items-center">
        <h4 class="text-xl font-medium text-slate-900 dark:text-slate-200">Thông báo của bạn</h4>
        <form action="{{ route('notifications.read.all') }}" method="POST">
            @csrf
            <button type="submit" class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                Đánh dấu tất cả đã đọc
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6 dashcode-data-table">
                <span class="col-span-8 hidden"></span>
                <span class="col-span-4 hidden"></span>
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700" id="notification-table">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Tiêu đề</th>
                                    <th scope="col" class="table-th">Nội dung</th>
                                    <th scope="col" class="table-th">Thời gian</th>
                                    <th scope="col" class="table-th">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700" id="notification-list">
                                @forelse ($notifications as $notification)
                                    <tr class="{{ $notification->read_at ? 'opacity-60' : 'font-bold bg-slate-50 dark:bg-slate-900' }}">
                                        <td class="table-td">
                                            {{ $notification->data['title'] ?? 'Thông báo mới' }}
                                            @if(isset($notification->data['is_important']) && $notification->data['is_important'])
                                                <span class="badge bg-danger-500 text-white rounded-full px-2 ml-2">Quan trọng</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            Sự kiện: {{ $notification->data['event_id'] ?? 'N/A' }} <br>
                                            Bắt đầu: {{ $notification->data['start_time'] ?? '' }}
                                        </td>
                                        <td class="table-td">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </td>
                                        <td class="table-td">
                                            @if($notification->read_at)
                                                <span class="text-slate-500">Đã đọc</span>
                                            @else
                                                <span class="text-primary-500">Mới</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="table-td text-center">Không có thông báo nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
    @push('scripts')
    {{-- Script xử lý Realtime với Laravel Echo --}}
    <script type="module">
        // Lấy ID của user hiện tại (được render từ server hoặc meta tag)
        const userId = "{{ auth()->id() }}";

        if (userId) {
            // Lắng nghe channel private mà EventReminderNotification đã định nghĩa
            // broadcastOn trả về: 'private-user-' . $this->event->created_by
            Echo.private('user-' + userId)
                .listen('.event.reminder', (e) => {
                    console.log('Realtime Notification:', e);
                    
                    // Hiển thị Toast hoặc Alert (Tuỳ thư viện Dashcode sử dụng, ví dụ Toastify)
                    // Toastify({ text: "Sự kiện mới: " + e.title, duration: 3000 }).showToast();

                    // Cập nhật giao diện danh sách (prepend vào bảng)
                    const newRow = `
                        <tr class="font-bold bg-slate-50 dark:bg-slate-900 animate-pulse">
                            <td class="table-td">${e.title} <span class="badge bg-success-500 text-white rounded-full px-2 ml-2">Mới</span></td>
                            <td class="table-td">Sự kiện: ${e.event_id} <br> Bắt đầu: ${e.start_time}</td>
                            <td class="table-td">Vừa xong</td>
                            <td class="table-td"><span class="text-primary-500">Mới</span></td>
                        </tr>
                    `;
                    
                    const list = document.getElementById('notification-list');
                    if(list) {
                        list.insertAdjacentHTML('afterbegin', newRow);
                    }
                });
        }
    </script>
    @endpush
</x-app-layout>