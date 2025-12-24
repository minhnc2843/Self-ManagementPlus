@php
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="relative md:block hidden">
    <button
        class="lg:h-[32px] lg:w-[32px] lg:bg-slate-50 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer
        rounded-full text-[20px] flex flex-col items-center justify-center"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        
      <iconify-icon class="animate-tada text-slate-800 dark:text-white text-xl" icon="heroicons-outline:bell"></iconify-icon>
        
        @if($unreadCount > 0)
            <span class="absolute -right-1 lg:top-0 -top-[6px] h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center
                justify-center rounded-full text-white z-[45]" id="notification-badge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu z-10 hidden bg-white divide-y divide-slate-100 dark:divide-slate-900 shadow w-[335px]
        dark:bg-slate-800 border dark:border-slate-900 !top-[18px] rounded-md overflow-hidden lrt:origin-top-right rtl:origin-top-left">
        
        <div class="flex items-center justify-between py-4 px-4">
            <h3 class="text-sm font-Inter font-medium text-slate-700 dark:text-white">Thông báo</h3>
            <a class="text-xs font-Inter font-normal underline text-slate-500 dark:text-white" href="{{ route('notifications.index') }}">Xem tất cả</a>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-900" role="none" id="notification-list">
            @forelse($notifications as $item)
                @php
                    // Lấy màu và icon từ data, nếu không có thì dùng mặc định
                    $color = $item->data['color'] ?? 'primary'; 
                    $icon = $item->data['icon'] ?? 'heroicons-outline:information-circle';
                    
                    // Map màu sang class Tailwind của Dashcode
                    $bgClass = match($color) {
                        'success' => 'bg-success-500',
                        'warning' => 'bg-warning-500',
                        'danger' => 'bg-danger-500',
                        'info' => 'bg-info-500',
                        default => 'bg-slate-500',
                    };
                    
                    // Class cho background item (chưa đọc thì đậm hơn chút/hoặc sáng hơn tùy theme)
                    $itemBg = is_null($item->read_at) ? 'bg-slate-50 dark:bg-slate-700/50' : '';
                @endphp

                <div class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm cursor-pointer {{ $itemBg }}">
                    <a href="{{ route('notifications.read', $item->id) }}" class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse relative">
                        
                       <div class="flex-none">
                        <div class="h-8 w-8 bg-white dark:bg-slate-700 rounded-full relative">
                            @if(is_null($item->read_at))
                                <span
                                    class="bg-danger-500 w-[10px] h-[10px] rounded-full border border-white
                                    dark:border-slate-700 inline-block absolute right-0 top-0"></span>
                            @endif
                            <img
                                src="/images/all-img/user.png"
                                alt="user"
                                class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                        </div>
                        </div>

                        <div class="flex-1">
                            <div class="text-slate-800 dark:text-slate-300 text-sm font-medium mb-1 before:w-full before:h-full before:absolute
                                before:top-0 before:left-0">
                                {{ $item->data['title'] ?? 'Thông báo mới' }}
                            </div>
                           <div class="text-xs hover:text-[#68768A] text-slate-600 dark:text-slate-300 mb-1">
                            {{ $item->data['message'] ?? '' }}
                        </div>
                        <div class="text-slate-400 dark:text-slate-400 text-xs">
                            {{ $item->created_at->diffForHumans() }}
                        </div>
                        </div>

                        @if(is_null($item->read_at))
                        <div class="flex-0">
                            <span class="h-2 w-2 bg-danger-500 border border-white dark:border-slate-700 rounded-full inline-block"></span>
                        </div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="text-slate-600 dark:text-slate-300 block w-full px-4 py-6 text-sm text-center" id="no-notification">
                    <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:bell-slash" class="text-4xl text-slate-400 mb-2"></iconify-icon>
                        <span>Không có thông báo mới</span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    // Đảm bảo bạn đã cài đặt Laravel Echo và Pusher/Reverb
    // Lắng nghe channel private của User hiện tại
    const userId = "{{ auth()->id() }}";
    
    if(typeof Echo !== 'undefined') {
        Echo.private('App.Models.User.' + userId)
            .notification((notification) => {
                console.log('New Notification:', notification);

                // 1. Cập nhật số lượng Badge
                let badge = document.getElementById('notification-badge');
                if (!badge) {
                    // Nếu chưa có badge (đang là 0), tạo mới
                    const btn = document.querySelector('button[data-bs-toggle="dropdown"]');
                    badge = document.createElement('span');
                    badge.className = "absolute -right-1 lg:top-0 -top-[6px] h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center justify-center rounded-full text-white z-[45]";
                    badge.id = "notification-badge";
                    badge.innerText = "0";
                    btn.appendChild(badge);
                }
                
                let count = parseInt(badge.innerText);
                if(isNaN(count)) count = 0;
                badge.innerText = count + 1;

                // 2. Xóa thông báo "Không có thông báo mới" nếu có
                const noNoti = document.getElementById('no-notification');
                if(noNoti) noNoti.remove();

                // 3. Chèn HTML thông báo mới vào đầu danh sách
                const list = document.getElementById('notification-list');
                const newHtml = `
                <div class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm cursor-pointer bg-slate-50 dark:bg-slate-700/50">
                    <a href="${notification.url}" class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse relative">
                       <div class="flex-none">
                        <div class="h-8 w-8 bg-white dark:bg-slate-700 rounded-full relative">
                            <span class="bg-danger-500 w-[10px] h-[10px] rounded-full border border-white dark:border-slate-700 inline-block absolute right-0 top-0"></span>
                            <img src="/images/all-img/user.png" alt="user" class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                        </div>
                        </div>
                        <div class="flex-1">
                            <div class="text-slate-800 dark:text-slate-300 text-sm font-medium mb-1">
                                ${notification.title}
                            </div>
                           <div class="text-xs hover:text-[#68768A] text-slate-600 dark:text-slate-300 mb-1">
                            ${notification.message}
                        </div>
                        <div class="text-slate-400 dark:text-slate-400 text-xs">
                            Vừa xong
                        </div>
                        </div>
                        <div class="flex-0">
                            <span class="h-2 w-2 bg-danger-500 border border-white dark:border-slate-700 rounded-full inline-block"></span>
                        </div>
                    </a>
                </div>`;
                
                list.insertAdjacentHTML('afterbegin', newHtml);
            });
    }
</script>
@endpush