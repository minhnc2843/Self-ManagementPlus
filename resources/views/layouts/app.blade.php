<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" class="light nav-floating">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <title>{{ config('app.name', 'dashcode') }}</title>

    {{-- Livewire Styles (V3 tự động inject, nhưng nếu lỗi giao diện thì uncomment dòng dưới) --}}
    {{-- @livewireStyles --}}

    {{-- 
        1. GỘP VITE: Load tất cả CSS/JS quan trọng ở đây để trình duyệt tải sớm. 
        Vite sẽ tự động chia tách CSS để render nhanh và JS để chạy sau.
    --}}
    @vite([
        'resources/css/app.scss', 
        'resources/js/app.js', 
    ])
    {{-- Lưu ý: main.js và store.js nên được import bên trong app.js để Vite bundle tốt hơn, hoặc chỉ load khi cần --}}
</head>

<body class="font-inter dashcode-app" id="body_class">
    <div class="app-wrapper">

        <x-sidebar-menu />

        <div class="flex flex-col justify-between min-h-screen">
            <div>
                <x-dashboard-header />

                <div class="content-wrapper transition-all duration-150 ltr:ml-0 xl:ltr:ml-[248px] rtl:mr-0 xl:rtl:mr-[248px]" id="content_wrapper">
                    <div class="page-content">
                        <div class="transition-all duration-150 container-fluid" id="page_layout">
                            <main id="content_layout">
                                <!-- Page Content -->
                                {{ $slot }}
                            </main>
                        </div>
                    </div>
                </div>
            </div>

            <x-dashboard-footer />
            </div>
    </div>

    {{-- Livewire Scripts (V3 tự động inject) --}}
    {{-- @livewireScripts --}}

    @stack('scripts')

    {{-- 
        2. SỬA LOGIC NOTIFICATION CHO SPA:
        - Đã xóa 1 đoạn script bị trùng lặp.
        - Thay 'DOMContentLoaded' bằng 'livewire:navigated' để code chạy lại mỗi khi chuyển trang mà không reload.
    --}}
    <script type="module">
        // Dùng type="module" để đảm bảo biến userId không bị lỗi redeclare khi Vite hot reload
        
        // Hàm xử lý logic thông báo
        const initNotifications = () => {
            const userId = {{ auth()->id() }};
            const list = document.getElementById('notification-list');
            const badge = document.getElementById('notification-count');

            // Kiểm tra nếu Echo chưa load hoặc không có list thì dừng
            if (!window.Echo || !list) return;

            // Hủy lắng nghe kênh cũ để tránh bị duplicate sự kiện khi chuyển trang
            window.Echo.leave(`App.Models.User.${userId}`);

            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    // 1️⃣ Badge tăng số
                    if (badge) {
                        const count = parseInt(badge.innerText || 0) + 1;
                        badge.innerText = count;
                        badge.classList.remove('hidden');
                    }

                    // 2️⃣ Append notification (Giữ nguyên HTML của bạn)
                    const html = `
                        <div class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm bg-slate-50 dark:bg-slate-700">
                            <div class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse relative">
                                <div class="flex-none">
                                    <div class="h-8 w-8 bg-white dark:bg-slate-700 rounded-full relative">
                                        <span class="bg-danger-500 w-[10px] h-[10px] rounded-full border border-white
                                            dark:border-slate-700 inline-block absolute right-0 top-0"></span>
                                        <img src="/images/all-img/user.png"
                                             class="block w-full h-full object-cover rounded-full border">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <a href="${notification.url}"
                                       class="text-slate-800 dark:text-slate-300 text-sm font-medium mb-1">
                                        ${notification.title}
                                    </a>
                                    <div class="text-xs text-slate-600 dark:text-slate-300 mb-1">
                                        ${notification.message}
                                    </div>
                                    <div class="text-slate-400 dark:text-slate-400 text-xs">
                                        just now
                                    </div>
                                </div>
                                <div class="flex-0">
                                    <span class="h-4 w-4 bg-danger-500 border border-white rounded-full text-[10px]
                                        flex items-center justify-center text-white">1</span>
                                </div>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('afterbegin', html);
                    
                    // 3️⃣ Toast thông báo (Optional)
                    // alert(notification.message); 
                });
        };

        // Chạy lần đầu khi tải trang
        document.addEventListener('DOMContentLoaded', initNotifications);

        // Chạy lại mỗi khi Livewire điều hướng (SPA mode - wire:navigate)
        document.addEventListener('livewire:navigated', initNotifications);
    </script>

</body>

</html>